<?php

/**
 * @extends PhabricatorOffsetPagedQuery<PhalanxThread>
 */
final class PhalanxThreadQuery extends PhabricatorOffsetPagedQuery {

  private $ids;
  private $objectPHIDs;
  private $externalThreadIDs;
  private $statuses;

  public function withIDs(array $ids) {
    $this->ids = $ids;
    return $this;
  }

  public function withObjectPHIDs(array $phids) {
    $this->objectPHIDs = $phids;
    return $this;
  }

  public function withExternalThreadIDs(array $ids) {
    $this->externalThreadIDs = $ids;
    return $this;
  }

  public function withStatuses(array $statuses) {
    $this->statuses = $statuses;
    return $this;
  }

  public function execute() {
    $table = new PhalanxThread();
    $conn = $table->establishConnection('r');

    $rows = queryfx_all(
      $conn,
      'SELECT * FROM %T %Q ORDER BY dateModified DESC %Q',
      $table->getTableName(),
      $this->buildWhereClause($conn),
      $this->buildLimitClause($conn));

    return $table->loadAllFromArray($rows);
  }

  private function buildWhereClause(AphrontDatabaseConnection $conn) {
    $where = array();

    if ($this->ids !== null) {
      $where[] = qsprintf(
        $conn,
        'id IN (%Ld)',
        $this->ids);
    }

    if ($this->objectPHIDs !== null) {
      $where[] = qsprintf(
        $conn,
        'objectPHID IN (%Ls)',
        $this->objectPHIDs);
    }

    if ($this->externalThreadIDs !== null) {
      $where[] = qsprintf(
        $conn,
        'externalThreadID IN (%Ls)',
        $this->externalThreadIDs);
    }

    if ($this->statuses !== null) {
      $where[] = qsprintf(
        $conn,
        'status IN (%Ls)',
        $this->statuses);
    }

    return $this->formatWhereClause($conn, $where);
  }

}
