<?php

final class PhalanxThreadSnapshot extends Phobject {

  private $externalThreadID;
  private $objectPHID;
  private $delegatedUserPHID;
  private $title;
  private $status;
  private $agentLabel;
  private $routeKind;
  private $permissionLevel;
  private $requiredApprovalKind;
  private $chatControlMode;
  private $reviewPolicy;
  private $backendKind;
  private $backendThreadID;
  private $externalResumeRef;
  private $properties = array();

  public static function newFromDictionary(array $dict) {
    $snapshot = new self();

    $snapshot->externalThreadID = PhalanxSnapshotData::requireString(
      $dict,
      'external_thread_id');
    $snapshot->objectPHID = PhalanxSnapshotData::requireString(
      $dict,
      'object_phid');
    $snapshot->delegatedUserPHID = PhalanxSnapshotData::optionalString(
      $dict,
      'delegated_user_phid');
    $snapshot->title = PhalanxSnapshotData::requireString($dict, 'title');
    $snapshot->status = PhalanxSnapshotData::requireString($dict, 'status');
    $snapshot->agentLabel = PhalanxSnapshotData::requireString(
      $dict,
      'agent_label');
    $snapshot->routeKind = PhalanxSnapshotData::optionalString(
      $dict,
      'route_kind');
    $snapshot->permissionLevel = PhalanxSnapshotData::optionalString(
      $dict,
      'permission_level');
    $snapshot->requiredApprovalKind = PhalanxSnapshotData::optionalString(
      $dict,
      'required_approval_kind');
    $snapshot->chatControlMode = PhalanxSnapshotData::optionalString(
      $dict,
      'chat_control_mode');
    $snapshot->reviewPolicy = PhalanxSnapshotData::optionalString(
      $dict,
      'review_policy');
    $snapshot->backendKind = PhalanxSnapshotData::optionalString(
      $dict,
      'backend_kind');
    $snapshot->backendThreadID = PhalanxSnapshotData::optionalString(
      $dict,
      'backend_thread_id');
    $snapshot->externalResumeRef = PhalanxSnapshotData::optionalString(
      $dict,
      'external_resume_ref');
    $snapshot->properties = PhalanxSnapshotData::optionalMap(
      $dict,
      'properties');

    return $snapshot;
  }

  public function getExternalThreadID() {
    return $this->externalThreadID;
  }

  public function getObjectPHID() {
    return $this->objectPHID;
  }

  public function getDelegatedUserPHID() {
    return $this->delegatedUserPHID;
  }

  public function getTitle() {
    return $this->title;
  }

  public function getStatus() {
    return $this->status;
  }

  public function getAgentLabel() {
    return $this->agentLabel;
  }

  public function getBackendKind() {
    return $this->backendKind;
  }

  public function getBackendThreadID() {
    return $this->backendThreadID;
  }

  public function getExternalResumeRef() {
    return $this->externalResumeRef;
  }

  public function newThreadProperties($agent_actor_phid) {
    $properties = $this->properties;

    $properties['agent_actor_phid'] = $agent_actor_phid;
    $properties['delegated_user_phid'] = $this->delegatedUserPHID;
    $properties['route_kind'] = $this->routeKind;
    $properties['permission_level'] = $this->permissionLevel;
    $properties['required_approval_kind'] = $this->requiredApprovalKind;
    $properties['chat_control_mode'] = $this->chatControlMode;
    $properties['review_policy'] = $this->reviewPolicy;

    return $properties;
  }

}
