<?php

final class PhalanxThreadReplyEngine extends Phobject {

  private $viewer;

  public function setViewer(PhabricatorUser $viewer) {
    $this->viewer = $viewer;
    return $this;
  }

  private function getViewer() {
    if (!$this->viewer) {
      throw new Exception(pht('Call setViewer() before replying to threads.'));
    }

    return $this->viewer;
  }

  public function replyFromDictionary(array $dict) {
    $snapshot = PhalanxReplySnapshot::newFromDictionary($dict);
    $thread = $this->loadThread($snapshot);
    $this->loadEditableObject($thread->getObjectPHID());

    $question = id(new PhalanxQuestion())->loadOneWhere(
      'threadID = %d AND isActive = %d ORDER BY dateModified DESC',
      $thread->getID(),
      1);

    $reply = id(new PhalanxReply())
      ->setThreadID($thread->getID())
      ->setQuestionID($question ? $question->getID() : null)
      ->setAuthorPHID($this->getViewer()->getPHID())
      ->setActionKind($snapshot->getActionKind())
      ->setMessageText($snapshot->getMessageText())
      ->setProperties($snapshot->getProperties())
      ->save();

    if ($question && $snapshot->shouldCloseQuestion()) {
      $question
        ->setIsActive(0)
        ->save();
    }

    return $reply;
  }

  private function loadThread(PhalanxReplySnapshot $snapshot) {
    $query = id(new PhalanxThreadQuery());

    if ($snapshot->getThreadID() !== null) {
      $query->withIDs(array($snapshot->getThreadID()));
    } else {
      $query->withExternalThreadIDs(array($snapshot->getExternalThreadID()));
    }

    $thread = $query->execute();
    $thread = head($thread);

    if (!$thread) {
      throw new Exception(pht('No matching Phalanx thread exists.'));
    }

    return $thread;
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

    $can_edit = PhabricatorPolicyFilter::hasCapability(
      $viewer,
      $object,
      PhabricatorPolicyCapability::CAN_EDIT);

    if (!$can_edit) {
      throw new Exception(
        pht(
          'User "%s" does not have permission to reply to Phalanx thread '.
          'for object "%s".',
          $viewer->getPHID(),
          $object->getPHID()));
    }

    return $object;
  }

}
