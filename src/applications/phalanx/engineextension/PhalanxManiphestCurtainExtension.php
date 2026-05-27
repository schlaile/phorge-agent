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
      $questions = $this->loadQuestions($threads);
      $replies = $this->loadReplies($threads);

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

        $question = head(idx($questions, $thread->getID(), array()));
        if ($question) {
          $content->addProperty(pht('Question'), $question->getPrompt());
          $content->addProperty(
            pht('Required Action'),
            $question->getRequiredActionKind());
        }

        $reply = head(idx($replies, $thread->getID(), array()));
        if ($reply) {
          $content->addProperty(
            pht('Latest Reply'),
            $this->getReplySummary($reply));
          $content->addProperty(
            pht('Delivery'),
            $this->getDeliveryStatusDisplayName($reply->getDeliveryStatus()));
        }
      }
    }

    return $this->newPanel()
      ->setHeaderText(pht('Phalanx'))
      ->setOrder(16000)
      ->appendChild($content);
  }

  private function loadQuestions(array $threads) {
    try {
      $questions = id(new PhalanxQuestion())->loadAllWhere(
        'threadID IN (%Ld) AND isActive = %d ORDER BY dateModified DESC',
        mpull($threads, 'getID'),
        1);
    } catch (AphrontQueryException $ex) {
      $questions = array();
    }

    return mgroup($questions, 'getThreadID');
  }

  private function loadReplies(array $threads) {
    try {
      $replies = id(new PhalanxReply())->loadAllWhere(
        'threadID IN (%Ld) ORDER BY dateModified DESC',
        mpull($threads, 'getID'));
    } catch (AphrontQueryException $ex) {
      $replies = array();
    }

    return mgroup($replies, 'getThreadID');
  }

  private function getReplySummary(PhalanxReply $reply) {
    if ($reply->getMessageText()) {
      return $reply->getMessageText();
    }

    return $reply->getActionKind();
  }

  private function getDeliveryStatusDisplayName($status) {
    $map = array(
      PhalanxReply::DELIVERY_RECORDED => pht('Recorded'),
      PhalanxReply::DELIVERY_QUEUED => pht('Queued'),
      PhalanxReply::DELIVERY_SENT => pht('Sent'),
      PhalanxReply::DELIVERY_ACKNOWLEDGED => pht('Acknowledged'),
      PhalanxReply::DELIVERY_FAILED => pht('Failed'),
    );

    return idx($map, $status, $status);
  }

}
