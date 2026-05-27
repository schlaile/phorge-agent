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
      $this->createPendingQuestion(
        $thread,
        PhalanxPendingQuestionSnapshot::newFromDictionary($pending_question));
    }

    foreach ($artifacts as $artifact) {
      if (!is_array($artifact)) {
        throw new Exception(pht('Each artifact must be a map.'));
      }
      $this->upsertArtifact(
        $thread,
        PhalanxArtifactSnapshot::newFromDictionary($artifact));
    }

    return $thread;
  }

  private function upsertThread(array $dict) {
    $snapshot = PhalanxThreadSnapshot::newFromDictionary($dict);

    $external_id = $snapshot->getExternalThreadID();
    $object_phid = $snapshot->getObjectPHID();
    $delegated_user_phid = $snapshot->getDelegatedUserPHID();

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

    $thread
      ->setObjectPHID($object_phid)
      ->setTitle($snapshot->getTitle())
      ->setStatus($snapshot->getStatus())
      ->setAgentLabel($snapshot->getAgentLabel())
      ->setBackendKind($snapshot->getBackendKind())
      ->setBackendThreadID($snapshot->getBackendThreadID())
      ->setExternalResumeRef($snapshot->getExternalResumeRef())
      ->setProperties(
        $snapshot->newThreadProperties($this->getViewer()->getPHID()))
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
    PhalanxPendingQuestionSnapshot $snapshot) {

    id(new PhalanxQuestion())
      ->setThreadID($thread->getID())
      ->setIsActive(1)
      ->setQuestionKind($snapshot->getQuestionKind())
      ->setPrompt($snapshot->getPrompt())
      ->setPrimaryContactPHID($snapshot->getPrimaryContactPHID())
      ->setResponseChannel($snapshot->getResponseChannel())
      ->setRequiredActionKind($snapshot->getRequiredActionKind())
      ->setRequiredAction($snapshot->getRequiredAction())
      ->setDefaultAction($snapshot->getDefaultAction())
      ->setProperties($snapshot->getProperties())
      ->save();
  }

  private function upsertArtifact(
    PhalanxThread $thread,
    PhalanxArtifactSnapshot $snapshot) {

    $external_id = $snapshot->getExternalArtifactID();

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
      ->setArtifactKind($snapshot->getArtifactKind())
      ->setLabel($snapshot->getLabel())
      ->setSummary($snapshot->getSummary())
      ->setFilePHID($snapshot->getFilePHID())
      ->setObjectPHID($snapshot->getObjectPHID())
      ->setExternalRef($snapshot->getExternalRef())
      ->setPayload($snapshot->getPayload())
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

}
