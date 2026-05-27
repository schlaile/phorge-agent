<?php

final class PhalanxDeliveryAckSnapshot extends Phobject {

  private $deliveryID;
  private $acknowledgementRef;
  private $properties = array();

  public static function newFromDictionary(array $dict) {
    $snapshot = new self();

    $snapshot->deliveryID = PhalanxSnapshotData::requirePositiveInteger(
      $dict,
      'delivery_id');
    $snapshot->acknowledgementRef = PhalanxSnapshotData::optionalString(
      $dict,
      'acknowledgement_ref');
    $snapshot->properties = PhalanxSnapshotData::optionalMap(
      $dict,
      'properties');

    return $snapshot;
  }

  public function getDeliveryID() {
    return $this->deliveryID;
  }

  public function getAcknowledgementRef() {
    return $this->acknowledgementRef;
  }

  public function getProperties() {
    return $this->properties;
  }

}
