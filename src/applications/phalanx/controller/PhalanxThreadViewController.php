<?php

final class PhalanxThreadViewController extends PhalanxController {

  public function handleRequest(AphrontRequest $request) {
    $viewer = $this->getViewer();
    $id = $request->getURIData('id');

    $thread = id(new PhalanxThreadQuery())
      ->withIDs(array($id))
      ->execute();
    $thread = head($thread);

    if (!$thread) {
      return new Aphront404Response();
    }

    $title = $thread->getTitle();

    $crumbs = $this->buildApplicationCrumbs();
    $crumbs->addTextCrumb($title);
    $crumbs->setBorder(true);

    $header = id(new PHUIHeaderView())
      ->setUser($viewer)
      ->setHeader($title)
      ->setHeaderIcon('fa-magic');

    $properties = id(new PHUIPropertyListView())
      ->setUser($viewer)
      ->addProperty(pht('Status'), $thread->getStatus())
      ->addProperty(pht('Agent'), $thread->getAgentLabel())
      ->addProperty(pht('Object PHID'), $thread->getObjectPHID())
      ->addProperty(pht('External Thread'), $thread->getExternalThreadID());

    if ($thread->getBackendKind()) {
      $properties->addProperty(pht('Backend'), $thread->getBackendKind());
    }

    if ($thread->getBackendThreadID()) {
      $properties->addProperty(
        pht('Backend Thread'),
        $thread->getBackendThreadID());
    }

    if ($thread->getExternalResumeRef()) {
      $properties->addProperty(
        pht('Resume Ref'),
        $thread->getExternalResumeRef());
    }

    $object_box = id(new PHUIObjectBoxView())
      ->setHeaderText(pht('Thread'))
      ->setBackground(PHUIObjectBoxView::BLUE_PROPERTY)
      ->addPropertyList($properties);

    $question_box = $this->buildQuestionBox($thread);
    $artifact_box = $this->buildArtifactBox($thread);

    $view = id(new PHUITwoColumnView())
      ->setHeader($header)
      ->setMainColumn(array(
        $object_box,
        $question_box,
        $artifact_box,
      ));

    return $this->newPage()
      ->setTitle($title)
      ->setCrumbs($crumbs)
      ->appendChild($view);
  }

  private function buildQuestionBox(PhalanxThread $thread) {
    $question = id(new PhalanxQuestion())->loadOneWhere(
      'threadID = %d AND isActive = %d ORDER BY dateModified DESC',
      $thread->getID(),
      1);

    if (!$question) {
      $content = id(new PHUIInfoView())
        ->setSeverity(PHUIInfoView::SEVERITY_NODATA)
        ->setErrors(array(pht('No active question.')));

      return id(new PHUIObjectBoxView())
        ->setHeaderText(pht('Pending Question'))
        ->appendChild($content);
    }

    $properties = id(new PHUIPropertyListView())
      ->setUser($this->getViewer())
      ->addProperty(pht('Kind'), $question->getQuestionKind())
      ->addProperty(pht('Prompt'), $question->getPrompt())
      ->addProperty(pht('Response'), $question->getResponseChannel())
      ->addProperty(pht('Required Action'), $question->getRequiredActionKind());

    if ($question->getPrimaryContactPHID()) {
      $properties->addProperty(
        pht('Primary Contact'),
        $question->getPrimaryContactPHID());
    }

    if ($question->getRequiredAction()) {
      $properties->addProperty(
        pht('Action Detail'),
        $question->getRequiredAction());
    }

    return id(new PHUIObjectBoxView())
      ->setHeaderText(pht('Pending Question'))
      ->setBackground(PHUIObjectBoxView::BLUE_PROPERTY)
      ->addPropertyList($properties);
  }

  private function buildArtifactBox(PhalanxThread $thread) {
    $artifacts = id(new PhalanxArtifact())->loadAllWhere(
      'threadID = %d ORDER BY dateModified DESC LIMIT 20',
      $thread->getID());

    if (!$artifacts) {
      $content = id(new PHUIInfoView())
        ->setSeverity(PHUIInfoView::SEVERITY_NODATA)
        ->setErrors(array(pht('No artifacts.')));

      return id(new PHUIObjectBoxView())
        ->setHeaderText(pht('Artifacts'))
        ->appendChild($content);
    }

    $properties = id(new PHUIPropertyListView())
      ->setUser($this->getViewer());

    foreach ($artifacts as $artifact) {
      $properties->addSectionHeader($artifact->getLabel(), 'fa-file-o');
      $properties->addProperty(pht('Kind'), $artifact->getArtifactKind());

      if ($artifact->getSummary()) {
        $properties->addProperty(pht('Summary'), $artifact->getSummary());
      }

      if ($artifact->getObjectPHID()) {
        $properties->addProperty(pht('Object PHID'), $artifact->getObjectPHID());
      }

      if ($artifact->getFilePHID()) {
        $properties->addProperty(pht('File PHID'), $artifact->getFilePHID());
      }

      if ($artifact->getExternalRef()) {
        $properties->addProperty(
          pht('External Ref'),
          $artifact->getExternalRef());
      }
    }

    return id(new PHUIObjectBoxView())
      ->setHeaderText(pht('Artifacts'))
      ->setBackground(PHUIObjectBoxView::BLUE_PROPERTY)
      ->addPropertyList($properties);
  }

}
