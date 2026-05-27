<?php

final class PhalanxThreadSearchConduitAPIMethod
  extends PhalanxConduitAPIMethod {

  public function getAPIMethodName() {
    return 'phalanx.thread.search';
  }

  public function getMethodDescription() {
    return pht('Search Phalanx threads with active questions and artifacts.');
  }

  public function getMethodStatus() {
    return self::METHOD_STATUS_UNSTABLE;
  }

  protected function defineParamTypes() {
    return array(
      'ids' => 'optional list<id>',
      'external_thread_ids' => 'optional list<string>',
      'object_phids' => 'optional list<phid>',
      'statuses' => 'optional list<string>',
      'limit' => 'optional int',
      'offset' => 'optional int',
    );
  }

  protected function defineReturnType() {
    return 'dict<string, wild>';
  }

  protected function execute(ConduitAPIRequest $request) {
    $viewer = $request->getUser();

    $query = id(new PhalanxThreadQuery());

    $ids = $request->getValue('ids');
    if ($ids !== null) {
      $query->withIDs($ids);
    }

    $external_ids = $request->getValue('external_thread_ids');
    if ($external_ids !== null) {
      $query->withExternalThreadIDs($external_ids);
    }

    $object_phids = $request->getValue('object_phids');
    if ($object_phids !== null) {
      $query->withObjectPHIDs($object_phids);
    }

    $statuses = $request->getValue('statuses');
    if ($statuses !== null) {
      $query->withStatuses($statuses);
    }

    $limit = $request->getValue('limit');
    if ($limit !== null) {
      $query->setLimit($limit);
    }

    $offset = $request->getValue('offset');
    if ($offset !== null) {
      $query->setOffset($offset);
    }

    $threads = $query->execute();
    $threads = $this->filterVisibleThreads($viewer, $threads);

    $thread_ids = mpull($threads, 'getID');
    if ($thread_ids) {
      $questions = id(new PhalanxQuestion())->loadAllWhere(
        'threadID IN (%Ld) AND isActive = 1 ORDER BY dateModified DESC',
        $thread_ids);
      $artifacts = id(new PhalanxArtifact())->loadAllWhere(
        'threadID IN (%Ld) ORDER BY dateModified DESC',
        $thread_ids);
      $replies = id(new PhalanxReply())->loadAllWhere(
        'threadID IN (%Ld) ORDER BY dateModified DESC',
        $thread_ids);
      $reply_ids = mpull($replies, 'getID');
      if ($reply_ids) {
        $deliveries = id(new PhalanxDelivery())->loadAllWhere(
          'replyID IN (%Ld) ORDER BY dateModified DESC',
          $reply_ids);
      } else {
        $deliveries = array();
      }
    } else {
      $questions = array();
      $artifacts = array();
      $replies = array();
      $deliveries = array();
    }

    $serializer = id(new PhalanxThreadConduitSerializer())
      ->setQuestions($questions)
      ->setArtifacts($artifacts)
      ->setReplies($replies)
      ->setDeliveries($deliveries);

    $data = array();
    foreach ($threads as $thread) {
      $data[] = $serializer->serializeThread($thread);
    }

    return array(
      'data' => $data,
    );
  }

  private function filterVisibleThreads(
    PhabricatorUser $viewer,
    array $threads) {

    if (!$threads) {
      return array();
    }

    $objects = id(new PhabricatorObjectQuery())
      ->setViewer($viewer)
      ->withPHIDs(array_unique(mpull($threads, 'getObjectPHID')))
      ->execute();
    $objects = mpull($objects, null, 'getPHID');

    $visible = array();
    foreach ($threads as $thread) {
      if (isset($objects[$thread->getObjectPHID()])) {
        $visible[] = $thread;
      }
    }

    return $visible;
  }

}
