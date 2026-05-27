<?php

final class PhalanxDeliveryAckConduitAPIMethod
  extends PhalanxConduitAPIMethod {

  public function getAPIMethodName() {
    return 'phalanx.delivery.ack';
  }

  public function getMethodDescription() {
    return pht('Acknowledge that an external control plane processed a delivery.');
  }

  public function getMethodStatus() {
    return self::METHOD_STATUS_UNSTABLE;
  }

  protected function defineParamTypes() {
    return array(
      'delivery_id' => 'required id',
      'acknowledgement_ref' => 'optional string',
      'properties' => 'optional dict<string, wild>',
    );
  }

  protected function defineReturnType() {
    return 'dict<string, wild>';
  }

  protected function execute(ConduitAPIRequest $request) {
    $delivery = id(new PhalanxDeliveryAckEngine())
      ->setViewer($request->getUser())
      ->acknowledgeFromDictionary(array(
        'delivery_id' => $request->getValue('delivery_id'),
        'acknowledgement_ref' => $request->getValue('acknowledgement_ref'),
        'properties' => $request->getValue('properties', array()),
      ));

    return array(
      'id' => $delivery->getID(),
      'thread_id' => $delivery->getThreadID(),
      'reply_id' => $delivery->getReplyID(),
      'status' => $delivery->getStatus(),
      'reply_delivery_status' => PhalanxReply::DELIVERY_ACKNOWLEDGED,
    );
  }

}
