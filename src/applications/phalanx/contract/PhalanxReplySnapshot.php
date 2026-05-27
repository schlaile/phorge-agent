<?php

final class PhalanxReplySnapshot extends Phobject {

  private $threadID;
  private $externalThreadID;
  private $actionKind;
  private $messageText;
  private $closeQuestion;
  private $properties = array();

  public static function newFromDictionary(array $dict) {
    $snapshot = new self();

    $thread_id = idx($dict, 'thread_id');
    if ($thread_id !== null) {
      if (!is_int($thread_id) && !ctype_digit((string)$thread_id)) {
        throw new InvalidArgumentException(
          pht('Field "thread_id" must be an integer.'));
      }
      $snapshot->threadID = (int)$thread_id;
      if ($snapshot->threadID <= 0) {
        throw new InvalidArgumentException(
          pht('Field "thread_id" must be a positive integer.'));
      }
    }

    $snapshot->externalThreadID = PhalanxSnapshotData::optionalString(
      $dict,
      'external_thread_id');

    if ($snapshot->threadID === null && $snapshot->externalThreadID === null) {
      throw new InvalidArgumentException(
        pht('Provide either "thread_id" or "external_thread_id".'));
    }

    $snapshot->actionKind = PhalanxSnapshotData::requireString(
      $dict,
      'action_kind');
    $snapshot->messageText = PhalanxSnapshotData::optionalString(
      $dict,
      'message_text');
    $snapshot->closeQuestion = (bool)idx($dict, 'close_question', true);
    $snapshot->properties = PhalanxSnapshotData::optionalMap(
      $dict,
      'properties');

    return $snapshot;
  }

  public function getThreadID() {
    return $this->threadID;
  }

  public function getExternalThreadID() {
    return $this->externalThreadID;
  }

  public function getActionKind() {
    return $this->actionKind;
  }

  public function getMessageText() {
    return $this->messageText;
  }

  public function shouldCloseQuestion() {
    return $this->closeQuestion;
  }

  public function getProperties() {
    return $this->properties;
  }

}
