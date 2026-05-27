<?php

final class PhalanxDeliveryAckEngine extends Phobject {

  private $viewer;

  public function setViewer(PhabricatorUser $viewer) {
    $this->viewer = $viewer;
    return $this;
  }

  private function getViewer() {
    if (!$this->viewer) {
      throw new Exception(pht('Call setViewer() before acknowledging delivery.'));
    }

    return $this->viewer;
  }

  public function acknowledgeFromDictionary(array $dict) {
    $snapshot = PhalanxDeliveryAckSnapshot::newFromDictionary($dict);

    $delivery = id(new PhalanxDelivery())->load($snapshot->getDeliveryID());
    if (!$delivery) {
      throw new Exception(pht('No matching Phalanx delivery exists.'));
    }

    $thread = id(new PhalanxThread())->load($delivery->getThreadID());
    if (!$thread) {
      throw new Exception(pht('No matching Phalanx thread exists.'));
    }

    $this->loadEditableObject($thread->getObjectPHID());

    $ack = array(
      'epoch' => PhabricatorTime::getNow(),
      'actor_phid' => $this->getViewer()->getPHID(),
      'acknowledgement_ref' => $snapshot->getAcknowledgementRef(),
      'properties' => $snapshot->getProperties(),
    );

    $delivery
      ->markAcknowledged($ack)
      ->save();

    $reply = id(new PhalanxReply())->load($delivery->getReplyID());
    if ($reply) {
      $reply
        ->setDeliveryStatus(PhalanxReply::DELIVERY_ACKNOWLEDGED)
        ->save();
    }

    return $delivery;
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
          'User "%s" does not have permission to acknowledge Phalanx '.
          'delivery for object "%s".',
          $viewer->getPHID(),
          $object->getPHID()));
    }

    return $object;
  }

}
