<?php

final class PhalanxSnapshotData extends Phobject {

  public static function requireString(array $dict, $key) {
    $value = idx($dict, $key);
    if (!phutil_nonempty_string($value)) {
      throw new InvalidArgumentException(
        pht('Field "%s" must be a nonempty string.', $key));
    }

    return $value;
  }

  public static function optionalString(array $dict, $key) {
    $value = idx($dict, $key);
    if ($value === null) {
      return null;
    }

    return self::requireString($dict, $key);
  }

  public static function optionalMap(array $dict, $key) {
    $value = idx($dict, $key, array());
    if (!is_array($value)) {
      throw new InvalidArgumentException(
        pht('Field "%s" must be a map.', $key));
    }

    return $value;
  }

  public static function requirePositiveInteger(array $dict, $key) {
    $value = idx($dict, $key);
    if (!is_int($value) && !ctype_digit((string)$value)) {
      throw new InvalidArgumentException(
        pht('Field "%s" must be an integer.', $key));
    }

    $value = (int)$value;
    if ($value <= 0) {
      throw new InvalidArgumentException(
        pht('Field "%s" must be a positive integer.', $key));
    }

    return $value;
  }

  private function __construct() {
  }

}
