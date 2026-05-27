<?php

final class PhalanxApplication extends PhabricatorApplication {

  public function getName() {
    return pht('Phalanx');
  }

  public function getBaseURI() {
    return '/phalanx/';
  }

  public function getIcon() {
    return 'fa-magic';
  }

  public function getShortDescription() {
    return pht('Agent Formations and Work Products');
  }

  public function getApplicationGroup() {
    return self::GROUP_UTILITIES;
  }

  public function getRoutes() {
    return array(
      '/phalanx/' => array(
        '' => 'PhalanxHomeController',
        'thread/(?P<id>[1-9]\d*)/' => 'PhalanxThreadViewController',
      ),
    );
  }

}
