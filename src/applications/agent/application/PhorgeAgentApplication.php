<?php

final class PhorgeAgentApplication extends PhabricatorApplication {

  public function getName() {
    return pht('Agents');
  }

  public function getBaseURI() {
    return '/agent/';
  }

  public function getIcon() {
    return 'fa-magic';
  }

  public function getShortDescription() {
    return pht('Agent Threads and Work Products');
  }

  public function getApplicationGroup() {
    return self::GROUP_UTILITIES;
  }

  public function getRoutes() {
    return array(
      '/agent/' => array(
        '' => 'PhorgeAgentHomeController',
      ),
    );
  }

}
