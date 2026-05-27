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
    'PhalanxArtifactSnapshot' => 'applications/phalanx/contract/PhalanxArtifactSnapshot.php',
    'PhalanxArtifact' => 'applications/phalanx/storage/PhalanxArtifact.php',
    'PhalanxConduitAPIMethod' => 'applications/phalanx/conduit/PhalanxConduitAPIMethod.php',
    'PhalanxController' => 'applications/phalanx/controller/PhalanxController.php',
    'PhalanxDAO' => 'applications/phalanx/storage/PhalanxDAO.php',
    'PhalanxHomeController' => 'applications/phalanx/controller/PhalanxHomeController.php',
    'PhalanxManiphestCurtainExtension' => 'applications/phalanx/engineextension/PhalanxManiphestCurtainExtension.php',
    'PhalanxPendingQuestionSnapshot' => 'applications/phalanx/contract/PhalanxPendingQuestionSnapshot.php',
    'PhalanxQuestion' => 'applications/phalanx/storage/PhalanxQuestion.php',
    'PhalanxSQLPatchList' => 'applications/phalanx/storage/PhalanxSQLPatchList.php',
    'PhalanxSnapshotData' => 'applications/phalanx/contract/PhalanxSnapshotData.php',
    'PhalanxThread' => 'applications/phalanx/storage/PhalanxThread.php',
    'PhalanxThreadConduitSerializer' => 'applications/phalanx/conduit/PhalanxThreadConduitSerializer.php',
    'PhalanxThreadQuery' => 'applications/phalanx/query/PhalanxThreadQuery.php',
    'PhalanxThreadSearchConduitAPIMethod' => 'applications/phalanx/conduit/PhalanxThreadSearchConduitAPIMethod.php',
    'PhalanxThreadSnapshot' => 'applications/phalanx/contract/PhalanxThreadSnapshot.php',
    'PhalanxThreadSnapshotTestCase' => 'applications/phalanx/contract/__tests__/PhalanxThreadSnapshotTestCase.php',
    'PhalanxThreadUpsertConduitAPIMethod' => 'applications/phalanx/conduit/PhalanxThreadUpsertConduitAPIMethod.php',
    'PhalanxThreadUpsertEngine' => 'applications/phalanx/engine/PhalanxThreadUpsertEngine.php',
    'PhalanxThreadViewController' => 'applications/phalanx/controller/PhalanxThreadViewController.php',
  ),
  'function' => array(),
  'xmap' => array(
    'PhalanxApplication' => 'PhabricatorApplication',
    'PhalanxArtifactSnapshot' => 'Phobject',
    'PhalanxArtifact' => 'PhalanxDAO',
    'PhalanxConduitAPIMethod' => 'ConduitAPIMethod',
    'PhalanxController' => 'PhabricatorController',
    'PhalanxDAO' => 'PhabricatorLiskDAO',
    'PhalanxHomeController' => 'PhalanxController',
    'PhalanxManiphestCurtainExtension' => 'PHUICurtainExtension',
    'PhalanxPendingQuestionSnapshot' => 'Phobject',
    'PhalanxQuestion' => 'PhalanxDAO',
    'PhalanxSQLPatchList' => 'PhabricatorSQLPatchList',
    'PhalanxSnapshotData' => 'Phobject',
    'PhalanxThread' => 'PhalanxDAO',
    'PhalanxThreadConduitSerializer' => 'Phobject',
    'PhalanxThreadQuery' => 'PhabricatorOffsetPagedQuery',
    'PhalanxThreadSearchConduitAPIMethod' => 'PhalanxConduitAPIMethod',
    'PhalanxThreadSnapshot' => 'Phobject',
    'PhalanxThreadSnapshotTestCase' => 'PhutilTestCase',
    'PhalanxThreadUpsertConduitAPIMethod' => 'PhalanxConduitAPIMethod',
    'PhalanxThreadUpsertEngine' => 'Phobject',
    'PhalanxThreadViewController' => 'PhalanxController',
  ),
));
