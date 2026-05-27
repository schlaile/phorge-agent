<?php

final class PhalanxQuestion extends PhalanxDAO {

  protected $threadID;
  protected $isActive;
  protected $questionKind;
  protected $prompt;
  protected $primaryContactPHID;
  protected $responseChannel;
  protected $requiredActionKind;
  protected $requiredAction;
  protected $defaultAction;
  protected $properties = array();

  public function getTableName() {
    return 'phalanx_question';
  }

  protected function getConfiguration() {
    return array(
      self::CONFIG_SERIALIZATION => array(
        'properties' => self::SERIALIZATION_JSON,
      ),
      self::CONFIG_COLUMN_SCHEMA => array(
        'threadID' => 'uint32',
        'isActive' => 'bool',
        'questionKind' => 'text64',
        'prompt' => 'text',
        'primaryContactPHID' => 'phid?',
        'responseChannel' => 'text32',
        'requiredActionKind' => 'text64',
        'requiredAction' => 'text?',
        'defaultAction' => 'text64?',
      ),
      self::CONFIG_KEY_SCHEMA => array(
        'key_thread' => array(
          'columns' => array('threadID', 'isActive', 'dateModified'),
        ),
      ),
    ) + parent::getConfiguration();
  }

}
