<?php

final class PhalanxPendingQuestionSnapshot extends Phobject {

  private $questionKind;
  private $prompt;
  private $primaryContactPHID;
  private $responseChannel;
  private $requiredActionKind;
  private $requiredAction;
  private $defaultAction;
  private $properties = array();

  public static function newFromDictionary(array $dict) {
    $snapshot = new self();

    $snapshot->questionKind = PhalanxSnapshotData::requireString(
      $dict,
      'question_kind');
    $snapshot->prompt = PhalanxSnapshotData::requireString($dict, 'prompt');
    $snapshot->primaryContactPHID = PhalanxSnapshotData::optionalString(
      $dict,
      'primary_contact_phid');
    $snapshot->responseChannel = PhalanxSnapshotData::requireString(
      $dict,
      'response_channel');
    $snapshot->requiredActionKind = PhalanxSnapshotData::requireString(
      $dict,
      'required_action_kind');
    $snapshot->requiredAction = PhalanxSnapshotData::optionalString(
      $dict,
      'required_action');
    $snapshot->defaultAction = PhalanxSnapshotData::optionalString(
      $dict,
      'default_action');
    $snapshot->properties = PhalanxSnapshotData::optionalMap(
      $dict,
      'properties');

    return $snapshot;
  }

  public function getQuestionKind() {
    return $this->questionKind;
  }

  public function getPrompt() {
    return $this->prompt;
  }

  public function getPrimaryContactPHID() {
    return $this->primaryContactPHID;
  }

  public function getResponseChannel() {
    return $this->responseChannel;
  }

  public function getRequiredActionKind() {
    return $this->requiredActionKind;
  }

  public function getRequiredAction() {
    return $this->requiredAction;
  }

  public function getDefaultAction() {
    return $this->defaultAction;
  }

  public function getProperties() {
    return $this->properties;
  }

}
