<?php

final class PhalanxThread extends PhalanxDAO {

  const STATUS_RUNNING = 'running';
  const STATUS_AWAITING_CHAT = 'awaiting_chat';
  const STATUS_BLOCKED = 'blocked';
  const STATUS_DONE = 'done';

  protected $objectPHID;
  protected $externalThreadID;
  protected $title;
  protected $status;
  protected $agentLabel;
  protected $backendKind;
  protected $backendThreadID;
  protected $externalResumeRef;
  protected $properties = array();

  public function getTableName() {
    return 'phalanx_thread';
  }

  protected function getConfiguration() {
    return array(
      self::CONFIG_SERIALIZATION => array(
        'properties' => self::SERIALIZATION_JSON,
      ),
      self::CONFIG_COLUMN_SCHEMA => array(
        'externalThreadID' => 'text64',
        'title' => 'text255',
        'status' => 'text32',
        'agentLabel' => 'text64',
        'backendKind' => 'text64?',
        'backendThreadID' => 'text64?',
        'externalResumeRef' => 'text255?',
      ),
      self::CONFIG_KEY_SCHEMA => array(
        'key_external' => array(
          'columns' => array('externalThreadID'),
          'unique' => true,
        ),
        'key_object' => array(
          'columns' => array('objectPHID', 'status', 'dateModified'),
        ),
      ),
    ) + parent::getConfiguration();
  }

  public function getURI() {
    return urisprintf('/phalanx/thread/%d/', $this->getID());
  }

}
