<?php

final class PhalanxHomeController extends PhalanxController {

  public function handleRequest(AphrontRequest $request) {
    $title = pht('Phalanx');

    $content = id(new PHUIObjectBoxView())
      ->setHeaderText(pht('Phalanx'))
      ->appendChild(
        phutil_tag(
          'p',
          array(),
          pht(
            'Phalanx is an early provider-neutral work surface for delegated, '.
            'resumable agent work in Phorge.')));

    $content->appendChild(
      phutil_tag(
        'p',
        array(),
        pht(
          'The first vertical slice will show agent session status, pending '.
          'questions, required actions, artifacts, and backend resume context '.
          'on Phorge objects.')));

    return $this->newPage()
      ->setTitle($title)
      ->appendChild(array(
        $this->buildApplicationCrumbs(),
        $content,
      ));
  }

}
