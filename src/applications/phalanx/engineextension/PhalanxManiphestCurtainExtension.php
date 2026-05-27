<?php

final class PhalanxManiphestCurtainExtension
  extends PHUICurtainExtension {

  const EXTENSIONKEY = 'phalanx.maniphest';

  public function shouldEnableForObject($object) {
    return ($object instanceof ManiphestTask);
  }

  public function getExtensionApplication() {
    return new PhalanxApplication();
  }

  public function buildCurtainPanel($object) {
    $viewer = $this->getViewer();

    try {
      $threads = id(new PhalanxThreadQuery())
        ->withObjectPHIDs(array($object->getPHID()))
        ->setLimit(3)
        ->execute();
    } catch (AphrontQueryException $ex) {
      $threads = array();
    }

    if (!$threads) {
      $content = id(new PHUIInfoView())
        ->setSeverity(PHUIInfoView::SEVERITY_NODATA)
        ->setErrors(array(pht('No active Phalanx threads.')));
    } else {
      $content = id(new PHUIPropertyListView())
        ->setUser($viewer)
        ->setStacked(true);

      foreach ($threads as $thread) {
        $content->addSectionHeader(
          phutil_tag(
            'a',
            array(
              'href' => $thread->getURI(),
            ),
            $thread->getTitle()),
          'fa-magic');
        $content->addProperty(pht('Status'), $thread->getStatus());
        $content->addProperty(pht('Agent'), $thread->getAgentLabel());

        if ($thread->getBackendKind()) {
          $content->addProperty(pht('Backend'), $thread->getBackendKind());
        }

        if ($thread->getExternalResumeRef()) {
          $content->addProperty(
            pht('Resume'),
            $thread->getExternalResumeRef());
        }
      }
    }

    return $this->newPanel()
      ->setHeaderText(pht('Phalanx'))
      ->setOrder(16000)
      ->appendChild($content);
  }

}
