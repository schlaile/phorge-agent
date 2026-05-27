<?php

final class PhalanxArtifactSnapshot extends Phobject {

  private $externalArtifactID;
  private $artifactKind;
  private $label;
  private $summary;
  private $filePHID;
  private $objectPHID;
  private $externalRef;
  private $payload = array();

  public static function newFromDictionary(array $dict) {
    $snapshot = new self();

    $snapshot->externalArtifactID = PhalanxSnapshotData::requireString(
      $dict,
      'external_artifact_id');
    $snapshot->artifactKind = PhalanxSnapshotData::requireString(
      $dict,
      'artifact_kind');
    $snapshot->label = PhalanxSnapshotData::requireString($dict, 'label');
    $snapshot->summary = PhalanxSnapshotData::optionalString(
      $dict,
      'summary');
    $snapshot->filePHID = PhalanxSnapshotData::optionalString(
      $dict,
      'file_phid');
    $snapshot->objectPHID = PhalanxSnapshotData::optionalString(
      $dict,
      'object_phid');
    $snapshot->externalRef = PhalanxSnapshotData::optionalString(
      $dict,
      'external_ref');
    $snapshot->payload = PhalanxSnapshotData::optionalMap(
      $dict,
      'payload');

    return $snapshot;
  }

  public function getExternalArtifactID() {
    return $this->externalArtifactID;
  }

  public function getArtifactKind() {
    return $this->artifactKind;
  }

  public function getLabel() {
    return $this->label;
  }

  public function getSummary() {
    return $this->summary;
  }

  public function getFilePHID() {
    return $this->filePHID;
  }

  public function getObjectPHID() {
    return $this->objectPHID;
  }

  public function getExternalRef() {
    return $this->externalRef;
  }

  public function getPayload() {
    return $this->payload;
  }

}
