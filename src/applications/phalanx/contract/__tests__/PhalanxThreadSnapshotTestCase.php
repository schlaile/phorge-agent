<?php

final class PhalanxThreadSnapshotTestCase extends PhutilTestCase {

  public function testSnapshotNormalizesRoutingFields() {
    $snapshot = PhalanxThreadSnapshot::newFromDictionary(
      array(
        'external_thread_id' => 'session-1',
        'object_phid' => 'PHID-TASK-example',
        'delegated_user_phid' => 'PHID-USER-owner',
        'title' => 'Investigate fallback',
        'status' => 'awaiting_chat',
        'agent_label' => 'alicia-blue',
        'route_kind' => 'primary_contact',
        'permission_level' => 'reply',
        'required_approval_kind' => 'merge_approval',
        'chat_control_mode' => 'delegate_scoped',
        'review_policy' => 'mandatory_review_on_delegate',
        'backend_kind' => 'codex_exec',
        'backend_thread_id' => 'thread-xyz',
        'external_resume_ref' => 'alicia://sessions/session-1',
        'properties' => array(
          'source' => 'unit',
        ),
      ));

    $this->assertEqual('session-1', $snapshot->getExternalThreadID());
    $this->assertEqual('PHID-TASK-example', $snapshot->getObjectPHID());
    $this->assertEqual('PHID-USER-owner', $snapshot->getDelegatedUserPHID());
    $this->assertEqual('Investigate fallback', $snapshot->getTitle());
    $this->assertEqual('awaiting_chat', $snapshot->getStatus());
    $this->assertEqual('alicia-blue', $snapshot->getAgentLabel());
    $this->assertEqual('codex_exec', $snapshot->getBackendKind());
    $this->assertEqual('thread-xyz', $snapshot->getBackendThreadID());

    $properties = $snapshot->newThreadProperties('PHID-USER-agent');
    $this->assertEqual('unit', $properties['source']);
    $this->assertEqual('PHID-USER-agent', $properties['agent_actor_phid']);
    $this->assertEqual(
      'PHID-USER-owner',
      $properties['delegated_user_phid']);
    $this->assertEqual('primary_contact', $properties['route_kind']);
    $this->assertEqual('reply', $properties['permission_level']);
    $this->assertEqual(
      'merge_approval',
      $properties['required_approval_kind']);
    $this->assertEqual('delegate_scoped', $properties['chat_control_mode']);
    $this->assertEqual(
      'mandatory_review_on_delegate',
      $properties['review_policy']);
  }

  public function testSnapshotRejectsMissingRequiredFields() {
    $caught = null;
    try {
      PhalanxThreadSnapshot::newFromDictionary(
        array(
          'object_phid' => 'PHID-TASK-example',
          'title' => 'Missing external ID',
          'status' => 'running',
          'agent_label' => 'alicia',
        ));
    } catch (Exception $ex) {
      $caught = $ex;
    }

    $this->assertCaught(InvalidArgumentException::class, $caught);
  }

  public function testSnapshotRejectsNonMapProperties() {
    $caught = null;
    try {
      PhalanxThreadSnapshot::newFromDictionary(
        array(
          'external_thread_id' => 'session-1',
          'object_phid' => 'PHID-TASK-example',
          'title' => 'Bad properties',
          'status' => 'running',
          'agent_label' => 'alicia',
          'properties' => 'not-a-map',
        ));
    } catch (Exception $ex) {
      $caught = $ex;
    }

    $this->assertCaught(InvalidArgumentException::class, $caught);
  }

}
