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
    'PhorgeAgentApplication' => 'applications/agent/application/PhorgeAgentApplication.php',
    'PhorgeAgentController' => 'applications/agent/controller/PhorgeAgentController.php',
    'PhorgeAgentHomeController' => 'applications/agent/controller/PhorgeAgentHomeController.php',
  ),
  'function' => array(),
  'xmap' => array(
    'PhorgeAgentApplication' => 'PhabricatorApplication',
    'PhorgeAgentController' => 'PhabricatorController',
    'PhorgeAgentHomeController' => 'PhorgeAgentController',
  ),
));
