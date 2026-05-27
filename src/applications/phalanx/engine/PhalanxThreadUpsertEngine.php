<?php

final class PhalanxThreadUpsertEngine extends Phobject {

  private $viewer;

  public function setViewer(PhabricatorUser $viewer) {
    $this->viewer = $viewer;
    return $this;
  }

  private function getViewer() {
    if (!$this->viewer) {
      throw new Exception(pht('Call setViewer() before upserting threads.'));
    }

    return $this->viewer;
  }

  public function upsertFromDictionary(array $dict) {
    $thread_dict = $this->requireMap($dict, 'thread');

    $pending_question = idx($dict, 'pending_question');
    if ($pending_question !== null && !is_array($pending_question)) {
      throw new Exception(pht('Field "pending_question" must be a map.'));
    }

    $artifacts = idx($dict, 'artifacts', array());
    if (!is_array($artifacts)) {
      throw new Exception(pht('Field "artifacts" must be a list.'));
    }

    $thread = $this->upsertThread($thread_dict);

    $clear_question = (bool)idx($dict, 'clear_pending_question', false);
    if ($clear_question || $pending_question !== null) {
      $this->clearPendingQuestion($thread);
    }

    if ($pending_question !== null) {
      $this->createPendingQuestion($thread, $pending_question);
    }

    foreach ($artifacts as $artifact) {
      if (!is_array($artifact)) {
        throw new Exception(pht('Each artifact must be a map.'));
      }
      $this->upsertArtifact($thread, $artifact);
    }

    return $thread;
  }

  private function upsertThread(array $dict) {
    $external_id = $this->requireString($dict, 'external_thread_id');
    $object_phid = $this->requireString($dict, 'object_phid');
    $delegated_user_phid = idx($dict, 'delegated_user_phid');
    if ($delegated_user_phid !== null) {
      $delegated_user_phid = $this->requireString(
        $dict,
        'delegated_user_phid');
    }

    $object = $this->loadEditableObject($object_phid);

    if ($delegated_user_phid !== null) {
      $delegated_user = $this->loadDelegatedUser($delegated_user_phid);
      $this->requireObjectEditCapability($delegated_user, $object);
    }

    $thread = id(new PhalanxThreadQuery())
      ->withExternalThreadIDs(array($external_id))
      ->execute();
    $thread = head($thread);

    if (!$thread) {
      $thread = new PhalanxThread();
      $thread->setExternalThreadID($external_id);
    }

    $properties = idx($dict, 'properties', array());
    if (!is_array($properties)) {
      throw new Exception(pht('Field "properties" must be a map.'));
    }

    $properties['agent_actor_phid'] = $this->getViewer()->getPHID();
    $properties['delegated_user_phid'] = $delegated_user_phid;
    $properties['route_kind'] = idx($dict, 'route_kind');
    $properties['permission_level'] = idx($dict, 'permission_level');
    $properties['required_approval_kind'] = idx(
      $dict,
      'required_approval_kind');
    $properties['chat_control_mode'] = idx($dict, 'chat_control_mode');
    $properties['review_policy'] = idx($dict, 'review_policy');

    $thread
      ->setObjectPHID($object_phid)
      ->setTitle($this->requireString($dict, 'title'))
      ->setStatus($this->requireString($dict, 'status'))
      ->setAgentLabel($this->requireString($dict, 'agent_label'))
      ->setBackendKind(idx($dict, 'backend_kind'))
      ->setBackendThreadID(idx($dict, 'backend_thread_id'))
      ->setExternalResumeRef(idx($dict, 'external_resume_ref'))
      ->setProperties($properties)
      ->save();

    return $thread;
  }

  private function clearPendingQuestion(PhalanxThread $thread) {
    queryfx(
      $thread->establishConnection('w'),
      'UPDATE %T SET isActive = %d WHERE threadID = %d AND isActive = %d',
      id(new PhalanxQuestion())->getTableName(),
      0,
      $thread->getID(),
      1);
  }

  private function createPendingQuestion(
    PhalanxThread $thread,
    array $dict) {

    id(new PhalanxQuestion())
      ->setThreadID($thread->getID())
      ->setIsActive(1)
      ->setQuestionKind($this->requireString($dict, 'question_kind'))
      ->setPrompt($this->requireString($dict, 'prompt'))
      ->setPrimaryContactPHID(idx($dict, 'primary_contact_phid'))
      ->setResponseChannel($this->requireString($dict, 'response_channel'))
      ->setRequiredActionKind(
        $this->requireString($dict, 'required_action_kind'))
      ->setRequiredAction(idx($dict, 'required_action'))
      ->setDefaultAction(idx($dict, 'default_action'))
      ->setProperties(idx($dict, 'properties', array()))
      ->save();
  }

  private function upsertArtifact(PhalanxThread $thread, array $dict) {
    $external_id = $this->requireString($dict, 'external_artifact_id');

    $artifact = id(new PhalanxArtifact())->loadOneWhere(
      'threadID = %d AND externalArtifactID = %s',
      $thread->getID(),
      $external_id);

    if (!$artifact) {
      $artifact = id(new PhalanxArtifact())
        ->setThreadID($thread->getID())
        ->setExternalArtifactID($external_id);
    }

    $artifact
      ->setArtifactKind($this->requireString($dict, 'artifact_kind'))
      ->setLabel($this->requireString($dict, 'label'))
      ->setSummary(idx($dict, 'summary'))
      ->setFilePHID(idx($dict, 'file_phid'))
      ->setObjectPHID(idx($dict, 'object_phid'))
      ->setExternalRef(idx($dict, 'external_ref'))
      ->setPayload(idx($dict, 'payload', array()))
      ->save();
  }

  private function loadEditableObject($phid) {
    $viewer = $this->getViewer();
    $object = id(new PhabricatorObjectQuery())
      ->setViewer($viewer)
      ->withPHIDs(array($phid))
      ->executeOne();

    if (!$object) {
      throw new Exception(
        pht(
          'No visible Phorge object exists with PHID "%s".',
          $phid));
    }

    $this->requireObjectEditCapability($viewer, $object);

    return $object;
  }

  private function loadDelegatedUser($phid) {
    $user = id(new PhabricatorPeopleQuery())
      ->setViewer(PhabricatorUser::getOmnipotentUser())
      ->withPHIDs(array($phid))
      ->executeOne();

    if (!$user) {
      throw new Exception(
        pht(
          'No delegated Phorge user exists with PHID "%s".',
          $phid));
    }

    return $user;
  }

  private function requireObjectEditCapability(
    PhabricatorUser $user,
    $object) {

    $can_edit = PhabricatorPolicyFilter::hasCapability(
      $user,
      $object,
      PhabricatorPolicyCapability::CAN_EDIT);

    if (!$can_edit) {
      throw new Exception(
        pht(
          'User "%s" does not have permission to attach Phalanx threads to '.
          'object "%s".',
          $user->getPHID(),
          $object->getPHID()));
    }
  }

  private function requireMap(array $dict, $key) {
    $value = idx($dict, $key);
    if (!is_array($value)) {
      throw new Exception(pht('Field "%s" must be a map.', $key));
    }

    return $value;
  }

  private function requireString(array $dict, $key) {
    $value = idx($dict, $key);
    if (!phutil_nonempty_string($value)) {
      throw new Exception(pht('Field "%s" must be a nonempty string.', $key));
    }

    return $value;
  }

}
