<?php

namespace PHPDO;

/**
 * Class DB
 * @package PHPDO
 */
class DB {

  /**
   * @var PHPDO
   */
  protected static $phpdo;

  /**
   * Set PHPDO instance
   *
   * @param PHPDO $PHPDO
   */
  public static function setInstance(PHPDO $PHPDO) {
    self::$phpdo = $PHPDO;
  }

  /**
   * Get PHPDO instance
   *
   * @return PHPDO
   *
   * @throws \Exception
   */
  public static function getInstance() : PHPDO {

    if (self::$phpdo instanceof PHPDO) {
      return self::$phpdo;
    }

    throw new \Exception("missing PHPDO object", 1524772643447);
  }

}