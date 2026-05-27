<?php

final class PhalanxThreadConduitSerializer extends Phobject {

  private $questions = array();
  private $artifacts = array();
  private $replies = array();

  public function setQuestions(array $questions) {
    $this->questions = mgroup($questions, 'getThreadID');
    return $this;
  }

  public function setArtifacts(array $artifacts) {
    $this->artifacts = mgroup($artifacts, 'getThreadID');
    return $this;
  }

  public function setReplies(array $replies) {
    $this->replies = mgroup($replies, 'getThreadID');
    return $this;
  }

  public function serializeThread(PhalanxThread $thread) {
    $questions = idx($this->questions, $thread->getID(), array());
    $question = head($questions);

    return array(
      'id' => $thread->getID(),
      'uri' => PhabricatorEnv::getProductionURI($thread->getURI()),
      'external_thread_id' => $thread->getExternalThreadID(),
      'object_phid' => $thread->getObjectPHID(),
      'title' => $thread->getTitle(),
      'status' => $thread->getStatus(),
      'agent_label' => $thread->getAgentLabel(),
      'backend_kind' => $thread->getBackendKind(),
      'backend_thread_id' => $thread->getBackendThreadID(),
      'external_resume_ref' => $thread->getExternalResumeRef(),
      'properties' => $thread->getProperties(),
      'date_created' => $thread->getDateCreated(),
      'date_modified' => $thread->getDateModified(),
      'pending_question' => $this->serializeQuestion($question),
      'artifacts' => $this->serializeArtifacts(
        idx($this->artifacts, $thread->getID(), array())),
      'replies' => $this->serializeReplies(
        idx($this->replies, $thread->getID(), array())),
    );
  }

  private function serializeQuestion($question) {
    if (!$question) {
      return null;
    }

    return array(
      'id' => $question->getID(),
      'question_kind' => $question->getQuestionKind(),
      'prompt' => $question->getPrompt(),
      'primary_contact_phid' => $question->getPrimaryContactPHID(),
      'response_channel' => $question->getResponseChannel(),
      'required_action_kind' => $question->getRequiredActionKind(),
      'required_action' => $question->getRequiredAction(),
      'default_action' => $question->getDefaultAction(),
      'properties' => $question->getProperties(),
      'date_created' => $question->getDateCreated(),
      'date_modified' => $question->getDateModified(),
    );
  }

  private function serializeArtifacts(array $artifacts) {
    $results = array();
    foreach ($artifacts as $artifact) {
      $results[] = array(
        'id' => $artifact->getID(),
        'external_artifact_id' => $artifact->getExternalArtifactID(),
        'artifact_kind' => $artifact->getArtifactKind(),
        'label' => $artifact->getLabel(),
        'summary' => $artifact->getSummary(),
        'file_phid' => $artifact->getFilePHID(),
        'object_phid' => $artifact->getObjectPHID(),
        'external_ref' => $artifact->getExternalRef(),
        'payload' => $artifact->getPayload(),
        'date_created' => $artifact->getDateCreated(),
        'date_modified' => $artifact->getDateModified(),
      );
    }

    return $results;
  }

  private function serializeReplies(array $replies) {
    $results = array();
    foreach ($replies as $reply) {
      $results[] = array(
        'id' => $reply->getID(),
        'question_id' => $reply->getQuestionID(),
        'author_phid' => $reply->getAuthorPHID(),
        'action_kind' => $reply->getActionKind(),
        'message_text' => $reply->getMessageText(),
        'properties' => $reply->getProperties(),
        'date_created' => $reply->getDateCreated(),
        'date_modified' => $reply->getDateModified(),
      );
    }

    return $results;
  }

}
