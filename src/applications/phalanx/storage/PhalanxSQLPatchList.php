<?php

final class PhalanxSQLPatchList extends PhabricatorSQLPatchList {

  public function getNamespace() {
    return 'phalanx';
  }

  public function getPatches() {
    $root = phutil_get_library_root('phorge-agent');
    $patch_root = $root.'/applications/phalanx/storage/patches';

    return array(
      'db.phalanx' => array(
        'type' => 'db',
        'name' => 'phalanx',
        'after' => array(),
      ),
    ) + $this->buildPatchesFromDirectory($patch_root);
  }

}
