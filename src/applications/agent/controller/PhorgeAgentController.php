<?php

abstract class PhorgeAgentController extends PhabricatorController {

  protected function buildApplicationCrumbs() {
    $crumbs = parent::buildApplicationCrumbs();

    $crumbs->addTextCrumb(
      pht('Agent Threads'),
      $this->getApplicationURI());

    return $crumbs;
  }

}
