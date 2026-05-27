<?php

final class PhalanxThreadReplyConduitAPIMethod
  extends PhalanxConduitAPIMethod {

  public function getAPIMethodName() {
    return 'phalanx.thread.reply';
  }

  public function getMethodDescription() {
    return pht('Record a user reply or control action on a Phalanx thread.');
  }

  public function getMethodStatus() {
    return self::METHOD_STATUS_UNSTABLE;
  }

  protected function defineParamTypes() {
    return array(
      'thread_id' => 'optional id',
      'external_thread_id' => 'optional string',
      'action_kind' => 'required string',
      'message_text' => 'optional string',
      'close_question' => 'optional bool',
      'properties' => 'optional dict<string, wild>',
    );
  }

  protected function defineReturnType() {
    return 'dict<string, wild>';
  }

  protected function execute(ConduitAPIRequest $request) {
    $reply = id(new PhalanxThreadReplyEngine())
      ->setViewer($request->getUser())
      ->replyFromDictionary(array(
        'thread_id' => $request->getValue('thread_id'),
        'external_thread_id' => $request->getValue('external_thread_id'),
        'action_kind' => $request->getValue('action_kind'),
        'message_text' => $request->getValue('message_text'),
        'close_question' => $request->getValue('close_question', true),
        'properties' => $request->getValue('properties', array()),
      ));

    return array(
      'id' => $reply->getID(),
      'thread_id' => $reply->getThreadID(),
      'question_id' => $reply->getQuestionID(),
      'author_phid' => $reply->getAuthorPHID(),
      'action_kind' => $reply->getActionKind(),
    );
  }

}
