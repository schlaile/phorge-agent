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

    $snapshot->externalThreadID = self::requireString(
      $dict,
      'external_thread_id');
    $snapshot->objectPHID = self::requireString($dict, 'object_phid');
    $snapshot->delegatedUserPHID = self::optionalString(
      $dict,
      'delegated_user_phid');
    $snapshot->title = self::requireString($dict, 'title');
    $snapshot->status = self::requireString($dict, 'status');
    $snapshot->agentLabel = self::requireString($dict, 'agent_label');
    $snapshot->routeKind = self::optionalString($dict, 'route_kind');
    $snapshot->permissionLevel = self::optionalString(
      $dict,
      'permission_level');
    $snapshot->requiredApprovalKind = self::optionalString(
      $dict,
      'required_approval_kind');
    $snapshot->chatControlMode = self::optionalString(
      $dict,
      'chat_control_mode');
    $snapshot->reviewPolicy = self::optionalString($dict, 'review_policy');
    $snapshot->backendKind = self::optionalString($dict, 'backend_kind');
    $snapshot->backendThreadID = self::optionalString(
      $dict,
      'backend_thread_id');
    $snapshot->externalResumeRef = self::optionalString(
      $dict,
      'external_resume_ref');

    $properties = idx($dict, 'properties', array());
    if (!is_array($properties)) {
      throw new InvalidArgumentException(
        pht('Field "properties" must be a map.'));
    }
    $snapshot->properties = $properties;

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

  private static function requireString(array $dict, $key) {
    $value = idx($dict, $key);
    if (!phutil_nonempty_string($value)) {
      throw new InvalidArgumentException(
        pht('Field "%s" must be a nonempty string.', $key));
    }

    return $value;
  }

  private static function optionalString(array $dict, $key) {
    $value = idx($dict, $key);
    if ($value === null) {
      return null;
    }

    return self::requireString($dict, $key);
  }

}
