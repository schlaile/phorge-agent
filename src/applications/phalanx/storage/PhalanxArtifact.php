<?php

final class PhalanxArtifact extends PhalanxDAO {

  protected $threadID;
  protected $externalArtifactID;
  protected $artifactKind;
  protected $label;
  protected $summary;
  protected $filePHID;
  protected $objectPHID;
  protected $externalRef;
  protected $payload = array();

  public function getTableName() {
    return 'phalanx_artifact';
  }

  protected function getConfiguration() {
    return array(
      self::CONFIG_SERIALIZATION => array(
        'payload' => self::SERIALIZATION_JSON,
      ),
      self::CONFIG_COLUMN_SCHEMA => array(
        'threadID' => 'uint32',
        'externalArtifactID' => 'text64',
        'artifactKind' => 'text64',
        'label' => 'text128',
        'summary' => 'text?',
        'filePHID' => 'phid?',
        'objectPHID' => 'phid?',
        'externalRef' => 'text255?',
      ),
      self::CONFIG_KEY_SCHEMA => array(
        'key_thread' => array(
          'columns' => array('threadID', 'dateModified'),
        ),
        'key_external' => array(
          'columns' => array('threadID', 'externalArtifactID'),
          'unique' => true,
        ),
      ),
    ) + parent::getConfiguration();
  }

}
