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
      ->setDeliveryStatus(PhalanxReply::DELIVERY_RECORDED)
      ->setProperties($snapshot->getProperties())
      ->save();

    $this->queueDelivery($thread, $reply);

    if ($question && $snapshot->shouldCloseQuestion()) {
      $question
        ->setIsActive(0)
        ->save();
    }

    return $reply;
  }

  private function queueDelivery(PhalanxThread $thread, PhalanxReply $reply) {
    $properties = $thread->getProperties();
    $uri = idx($properties, 'delivery_callback_uri');
    if (!phutil_nonempty_string($uri)) {
      return;
    }

    $payload = array(
      'type' => 'phalanx.reply',
      'thread' => array(
        'id' => $thread->getID(),
        'external_thread_id' => $thread->getExternalThreadID(),
        'object_phid' => $thread->getObjectPHID(),
      ),
      'reply' => array(
        'id' => $reply->getID(),
        'question_id' => $reply->getQuestionID(),
        'author_phid' => $reply->getAuthorPHID(),
        'action_kind' => $reply->getActionKind(),
        'message_text' => $reply->getMessageText(),
        'properties' => $reply->getProperties(),
        'date_created' => $reply->getDateCreated(),
      ),
    );

    $delivery = PhalanxDelivery::initializeNewDelivery($thread, $reply, $uri)
      ->setPayload($payload);

    $retry_mode = idx($properties, 'delivery_retry_mode');
    if ($retry_mode === PhalanxDelivery::RETRY_FOREVER) {
      $delivery->setRetryMode(PhalanxDelivery::RETRY_FOREVER);
    }

    $hmac_key = idx($properties, 'delivery_hmac_key');
    if (phutil_nonempty_string($hmac_key)) {
      $delivery->setHMACKey($hmac_key);
    }

    $delivery
      ->save()
      ->queueCall();

    $reply
      ->setDeliveryStatus(PhalanxReply::DELIVERY_QUEUED)
      ->save();
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
