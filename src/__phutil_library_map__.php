<?php

/**
 * This file is currently maintained by hand for the small initial skeleton.
 * Use `arc liberate` to rebuild it once arcanist is available in the
 * development environment.
 *
 * @generated
 * @phutil-library-version 2
 */
phutil_register_library_map(array(
  '__library_version__' => 2,
  'class' => array(
    'PhalanxApplication' => 'applications/phalanx/application/PhalanxApplication.php',
    'PhalanxArtifact' => 'applications/phalanx/storage/PhalanxArtifact.php',
    'PhalanxConduitAPIMethod' => 'applications/phalanx/conduit/PhalanxConduitAPIMethod.php',
    'PhalanxController' => 'applications/phalanx/controller/PhalanxController.php',
    'PhalanxDAO' => 'applications/phalanx/storage/PhalanxDAO.php',
    'PhalanxHomeController' => 'applications/phalanx/controller/PhalanxHomeController.php',
    'PhalanxManiphestCurtainExtension' => 'applications/phalanx/engineextension/PhalanxManiphestCurtainExtension.php',
    'PhalanxQuestion' => 'applications/phalanx/storage/PhalanxQuestion.php',
    'PhalanxSQLPatchList' => 'applications/phalanx/storage/PhalanxSQLPatchList.php',
    'PhalanxThread' => 'applications/phalanx/storage/PhalanxThread.php',
    'PhalanxThreadQuery' => 'applications/phalanx/query/PhalanxThreadQuery.php',
    'PhalanxThreadUpsertConduitAPIMethod' => 'applications/phalanx/conduit/PhalanxThreadUpsertConduitAPIMethod.php',
    'PhalanxThreadUpsertEngine' => 'applications/phalanx/engine/PhalanxThreadUpsertEngine.php',
    'PhalanxThreadViewController' => 'applications/phalanx/controller/PhalanxThreadViewController.php',
  ),
  'function' => array(),
  'xmap' => array(
    'PhalanxApplication' => 'PhabricatorApplication',
    'PhalanxArtifact' => 'PhalanxDAO',
    'PhalanxConduitAPIMethod' => 'ConduitAPIMethod',
    'PhalanxController' => 'PhabricatorController',
    'PhalanxDAO' => 'PhabricatorLiskDAO',
    'PhalanxHomeController' => 'PhalanxController',
    'PhalanxManiphestCurtainExtension' => 'PHUICurtainExtension',
    'PhalanxQuestion' => 'PhalanxDAO',
    'PhalanxSQLPatchList' => 'PhabricatorSQLPatchList',
    'PhalanxThread' => 'PhalanxDAO',
    'PhalanxThreadQuery' => 'PhabricatorOffsetPagedQuery',
    'PhalanxThreadUpsertConduitAPIMethod' => 'PhalanxConduitAPIMethod',
    'PhalanxThreadUpsertEngine' => 'Phobject',
    'PhalanxThreadViewController' => 'PhalanxController',
  ),
));
