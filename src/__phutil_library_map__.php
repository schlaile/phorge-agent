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
    'PhalanxController' => 'applications/phalanx/controller/PhalanxController.php',
    'PhalanxHomeController' => 'applications/phalanx/controller/PhalanxHomeController.php',
  ),
  'function' => array(),
  'xmap' => array(
    'PhalanxApplication' => 'PhabricatorApplication',
    'PhalanxController' => 'PhabricatorController',
    'PhalanxHomeController' => 'PhalanxController',
  ),
));
