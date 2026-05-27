<?php

final class PhalanxReply extends PhalanxDAO {

  const DELIVERY_RECORDED = 'recorded';
  const DELIVERY_QUEUED = 'queued';
  const DELIVERY_SENT = 'sent';
  const DELIVERY_ACKNOWLEDGED = 'acknowledged';
  const DELIVERY_FAILED = 'failed';

  protected $threadID;
  protected $questionID;
  protected $authorPHID;
  protected $actionKind;
  protected $messageText;
  protected $deliveryStatus;
  protected $properties = array();

  public function getTableName() {
    return 'phalanx_reply';
  }

  protected function getConfiguration() {
    return array(
      self::CONFIG_SERIALIZATION => array(
        'properties' => self::SERIALIZATION_JSON,
      ),
      self::CONFIG_COLUMN_SCHEMA => array(
        'threadID' => 'uint32',
        'questionID' => 'uint32?',
        'authorPHID' => 'phid',
        'actionKind' => 'text64',
        'messageText' => 'text?',
        'deliveryStatus' => 'text32',
      ),
      self::CONFIG_KEY_SCHEMA => array(
        'key_thread' => array(
          'columns' => array('threadID', 'dateModified'),
        ),
      ),
    ) + parent::getConfiguration();
  }

}
