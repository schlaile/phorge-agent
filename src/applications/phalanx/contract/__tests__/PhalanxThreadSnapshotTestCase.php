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

  public function testPendingQuestionSnapshotNormalizesFields() {
    $snapshot = PhalanxPendingQuestionSnapshot::newFromDictionary(
      array(
        'question_kind' => 'implementation_clarification',
        'prompt' => 'Choose API variant.',
        'primary_contact_phid' => 'PHID-USER-owner',
        'response_channel' => 'chat',
        'required_action_kind' => 'reply_in_chat',
        'required_action' => 'Reply with A or B.',
        'default_action' => 'wait_for_response',
        'properties' => array(
          'required_approval_kind' => 'security_approval',
        ),
      ));

    $this->assertEqual(
      'implementation_clarification',
      $snapshot->getQuestionKind());
    $this->assertEqual('Choose API variant.', $snapshot->getPrompt());
    $this->assertEqual(
      'PHID-USER-owner',
      $snapshot->getPrimaryContactPHID());
    $this->assertEqual('chat', $snapshot->getResponseChannel());
    $this->assertEqual('reply_in_chat', $snapshot->getRequiredActionKind());
    $this->assertEqual('Reply with A or B.', $snapshot->getRequiredAction());
    $this->assertEqual('wait_for_response', $snapshot->getDefaultAction());

    $properties = $snapshot->getProperties();
    $this->assertEqual(
      'security_approval',
      $properties['required_approval_kind']);
  }

  public function testArtifactSnapshotNormalizesFields() {
    $snapshot = PhalanxArtifactSnapshot::newFromDictionary(
      array(
        'external_artifact_id' => 'artifact-1',
        'artifact_kind' => 'test_run',
        'label' => 'Unit tests',
        'summary' => 'All tests passed.',
        'file_phid' => 'PHID-FILE-log',
        'object_phid' => 'PHID-HMBD-build',
        'external_ref' => 'alicia://artifacts/artifact-1',
        'payload' => array(
          'status' => 'passed',
        ),
      ));

    $this->assertEqual('artifact-1', $snapshot->getExternalArtifactID());
    $this->assertEqual('test_run', $snapshot->getArtifactKind());
    $this->assertEqual('Unit tests', $snapshot->getLabel());
    $this->assertEqual('All tests passed.', $snapshot->getSummary());
    $this->assertEqual('PHID-FILE-log', $snapshot->getFilePHID());
    $this->assertEqual('PHID-HMBD-build', $snapshot->getObjectPHID());
    $this->assertEqual(
      'alicia://artifacts/artifact-1',
      $snapshot->getExternalRef());

    $payload = $snapshot->getPayload();
    $this->assertEqual('passed', $payload['status']);
  }

  public function testArtifactSnapshotRejectsNonMapPayload() {
    $caught = null;
    try {
      PhalanxArtifactSnapshot::newFromDictionary(
        array(
          'external_artifact_id' => 'artifact-1',
          'artifact_kind' => 'test_run',
          'label' => 'Bad payload',
          'payload' => 'not-a-map',
        ));
    } catch (Exception $ex) {
      $caught = $ex;
    }

    $this->assertCaught(InvalidArgumentException::class, $caught);
  }

}
