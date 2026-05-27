<?php

final class PhalanxDelivery extends PhalanxDAO {

  const STATUS_QUEUED = 'queued';
  const STATUS_SENT = 'sent';
  const STATUS_ACKNOWLEDGED = 'acknowledged';
  const STATUS_FAILED = 'failed';

  const RESULT_NONE = 'none';
  const RESULT_OKAY = 'okay';
  const RESULT_FAIL = 'fail';

  const RETRY_NEVER = 'never';
  const RETRY_FOREVER = 'forever';

  const ERRORTYPE_HOOK = 'hook';
  const ERRORTYPE_HTTP = 'http';
  const ERRORTYPE_TIMEOUT = 'timeout';

  const ERROR_URI = 'uri';

  protected $threadID;
  protected $replyID;
  protected $deliveryURI;
  protected $status;
  protected $retryMode;
  protected $lastRequestResult;
  protected $lastRequestEpoch;
  protected $errorType;
  protected $errorCode;
  protected $attemptCount;
  protected $properties = array();

  public function getTableName() {
    return 'phalanx_delivery';
  }

  protected function getConfiguration() {
    return array(
      self::CONFIG_SERIALIZATION => array(
        'properties' => self::SERIALIZATION_JSON,
      ),
      self::CONFIG_COLUMN_SCHEMA => array(
        'threadID' => 'uint32',
        'replyID' => 'uint32',
        'deliveryURI' => 'text255',
        'status' => 'text32',
        'retryMode' => 'text32',
        'lastRequestResult' => 'text32',
        'lastRequestEpoch' => 'epoch?',
        'errorType' => 'text32?',
        'errorCode' => 'text64?',
        'attemptCount' => 'uint32',
      ),
      self::CONFIG_KEY_SCHEMA => array(
        'key_reply' => array(
          'columns' => array('replyID', 'dateModified'),
        ),
        'key_status' => array(
          'columns' => array('status', 'dateModified'),
        ),
      ),
    ) + parent::getConfiguration();
  }

  public static function initializeNewDelivery(
    PhalanxThread $thread,
    PhalanxReply $reply,
    $uri) {

    return id(new self())
      ->setThreadID($thread->getID())
      ->setReplyID($reply->getID())
      ->setDeliveryURI($uri)
      ->setStatus(self::STATUS_QUEUED)
      ->setRetryMode(self::RETRY_NEVER)
      ->setLastRequestResult(self::RESULT_NONE)
      ->setAttemptCount(0);
  }

  public function setPayload(array $payload) {
    $properties = $this->getProperties();
    $properties['payload'] = $payload;
    return $this->setProperties($properties);
  }

  public function getPayload() {
    return idx($this->getProperties(), 'payload', array());
  }

  public function setHMACKey($key) {
    $properties = $this->getProperties();
    $properties['hmac_key'] = $key;
    return $this->setProperties($properties);
  }

  public function getHMACKey() {
    return idx($this->getProperties(), 'hmac_key');
  }

  public function markAcknowledged(array $properties) {
    $delivery_properties = $this->getProperties();
    $delivery_properties['acknowledgement'] = $properties;

    return $this
      ->setStatus(self::STATUS_ACKNOWLEDGED)
      ->setProperties($delivery_properties);
  }

  public function queueCall() {
    PhabricatorWorker::scheduleTask(
      'PhalanxDeliveryWorker',
      array(
        'deliveryID' => $this->getID(),
      ));

    return $this;
  }

}
