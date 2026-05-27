<?php

final class PhalanxDeliveryWorker extends PhabricatorWorker {

  protected function doWork() {
    $data = $this->getTaskData();
    $delivery_id = idx($data, 'deliveryID');

    $delivery = id(new PhalanxDelivery())->load($delivery_id);
    if (!$delivery) {
      throw new PhabricatorWorkerPermanentFailureException(
        pht('Unable to load Phalanx delivery "%s".', $delivery_id));
    }

    if ($delivery->getStatus() !== PhalanxDelivery::STATUS_QUEUED) {
      throw new PhabricatorWorkerPermanentFailureException(
        pht(
          'Phalanx delivery "%s" is not queued.',
          $delivery->getID()));
    }

    $uri = $delivery->getDeliveryURI();
    try {
      PhabricatorEnv::requireValidRemoteURIForFetch(
        $uri,
        array(
          'http',
          'https',
        ));
    } catch (Exception $ex) {
      $this->failDelivery(
        $delivery,
        PhalanxDelivery::ERRORTYPE_HOOK,
        PhalanxDelivery::ERROR_URI);
      throw new PhabricatorWorkerPermanentFailureException($ex->getMessage());
    }

    $lock = PhabricatorGlobalLock::newLock('phalanx-delivery('.$uri.')');
    try {
      $lock->lock();
    } catch (Exception $ex) {
      phlog($ex);
      throw new PhabricatorWorkerYieldException(15);
    }

    $caught = null;
    try {
      $this->deliverWithLock($delivery);
    } catch (Exception $ex) {
      $caught = $ex;
    }

    $lock->unlock();

    if ($caught) {
      throw $caught;
    }
  }

  private function deliverWithLock(PhalanxDelivery $delivery) {
    $payload = id(new PhutilJSON())->encodeFormatted($delivery->getPayload());
    $future = id(new HTTPSFuture($delivery->getDeliveryURI()))
      ->setMethod('POST')
      ->addHeader('Content-Type', 'application/json')
      ->addHeader('X-Phalanx-Delivery-ID', $delivery->getID())
      ->setTimeout(15)
      ->setData($payload);

    $hmac_key = $delivery->getHMACKey();
    if ($hmac_key !== null) {
      $future->addHeader(
        'X-Phalanx-Webhook-Signature',
        PhabricatorHash::digestHMACSHA256($payload, $hmac_key));
    }

    list($status) = $future->resolve();

    if ($status->isTimeout()) {
      $error_type = PhalanxDelivery::ERRORTYPE_TIMEOUT;
    } else {
      $error_type = PhalanxDelivery::ERRORTYPE_HTTP;
    }
    $error_code = $status->getStatusCode();

    $delivery
      ->setAttemptCount($delivery->getAttemptCount() + 1)
      ->setLastRequestEpoch(PhabricatorTime::getNow())
      ->setErrorType($error_type)
      ->setErrorCode($error_code);

    if ($status->isTimeout() || $status->isError()) {
      $delivery
        ->setLastRequestResult(PhalanxDelivery::RESULT_FAIL)
        ->save();

      $this->updateReplyDeliveryStatus(
        $delivery,
        PhalanxReply::DELIVERY_FAILED);

      if ($delivery->getRetryMode() === PhalanxDelivery::RETRY_FOREVER) {
        throw new Exception(
          pht(
            'Phalanx delivery "%s" failed and will be retried.',
            $delivery->getID()));
      }

      $delivery
        ->setStatus(PhalanxDelivery::STATUS_FAILED)
        ->save();

      throw new PhabricatorWorkerPermanentFailureException(
        pht(
          'Phalanx delivery "%s" failed and will not be retried.',
          $delivery->getID()));
    }

    $delivery
      ->setLastRequestResult(PhalanxDelivery::RESULT_OKAY)
      ->setStatus(PhalanxDelivery::STATUS_SENT)
      ->save();

    $this->updateReplyDeliveryStatus($delivery, PhalanxReply::DELIVERY_SENT);
  }

  private function failDelivery(
    PhalanxDelivery $delivery,
    $error_type,
    $error_code) {

    $delivery
      ->setStatus(PhalanxDelivery::STATUS_FAILED)
      ->setLastRequestResult(PhalanxDelivery::RESULT_FAIL)
      ->setLastRequestEpoch(PhabricatorTime::getNow())
      ->setErrorType($error_type)
      ->setErrorCode($error_code)
      ->save();

    $this->updateReplyDeliveryStatus($delivery, PhalanxReply::DELIVERY_FAILED);
  }

  private function updateReplyDeliveryStatus(
    PhalanxDelivery $delivery,
    $status) {

    $reply = id(new PhalanxReply())->load($delivery->getReplyID());
    if (!$reply) {
      return;
    }

    $reply
      ->setDeliveryStatus($status)
      ->save();
  }

}
