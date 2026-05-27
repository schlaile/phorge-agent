<?php

abstract class PhalanxController extends PhabricatorController {

  protected function buildApplicationCrumbs() {
    $crumbs = parent::buildApplicationCrumbs();

    $crumbs->addTextCrumb(
      pht('Phalanx'),
      $this->getApplicationURI());

    return $crumbs;
  }

}
