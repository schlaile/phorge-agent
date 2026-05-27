<?php

final class PhalanxThreadUpsertConduitAPIMethod
  extends PhalanxConduitAPIMethod {

  public function getAPIMethodName() {
    return 'phalanx.thread.upsert';
  }

  public function getMethodDescription() {
    return pht(
      'Create or update a Phalanx thread, its active question, and artifacts.');
  }

  public function getMethodStatus() {
    return self::METHOD_STATUS_UNSTABLE;
  }

  protected function defineParamTypes() {
    return array(
      'thread' => 'required dict<string, wild>',
      'pending_question' => 'optional dict<string, wild>',
      'clear_pending_question' => 'optional bool',
      'artifacts' => 'optional list<dict<string, wild>>',
    );
  }

  protected function defineReturnType() {
    return 'dict<string, wild>';
  }

  protected function execute(ConduitAPIRequest $request) {
    $thread = id(new PhalanxThreadUpsertEngine())
      ->setViewer($request->getUser())
      ->upsertFromDictionary(array(
        'thread' => $request->getValue('thread'),
        'pending_question' => $request->getValue('pending_question'),
        'clear_pending_question' => $request->getValue(
          'clear_pending_question',
          false),
        'artifacts' => $request->getValue('artifacts', array()),
      ));

    return array(
      'id' => $thread->getID(),
      'uri' => PhabricatorEnv::getProductionURI($thread->getURI()),
      'external_thread_id' => $thread->getExternalThreadID(),
      'object_phid' => $thread->getObjectPHID(),
      'status' => $thread->getStatus(),
    );
  }

}
