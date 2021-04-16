<?php

namespace PHPDO;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

/**
 * PHPDO
 * A lightweight PHP PDO database singleton wrapper class for everyone
 *
 * @author Nico Hemkes
 * @license MIT License
 * @link https://github.com/turbopixel/PHPDO-Database
 */
class PHPDO {

  /**
   * @var PHPDO
   */
  protected static $_instance;

  /**
   * Log queries
   * @var bool
   */
  public static $logging = false;

  /**
   * @var PDO
   */
  private static $PDO;

  /**
   * @var array
   */
  private static $logs = [];

  /**
   * Constructor
   *
   * @throws Exception
   */
  private function __construct() {
  }

  /**
   * Clone
   */
  protected function __clone() {
  }

  /**
   * Internal php error log
   *
   * @param string $message
   */
  private static function logError(string $message) {
    if (function_exists('error_log')) {
      error_log($message);
    }
  }

  /**
   * Singleton
   *
   * @return PHPDO
   */
  public static function get() : self {

    if (self::$_instance === NULL) {
      self::$_instance = new self();
    }

    return self::$_instance;
  }

  /**
   * Get pdo object
   *
   * @return PDO
   *
   * @throws Exception
   */
  public function getPdo() : PDO {

    if (self::$PDO instanceof PDO) {
      return self::$PDO;
    }

    throw new Exception("The database connection could not be established.", 1523905129030);
  }

  /**
   * Create database connection
   *
   * @param string $host Hostname
   * @param string $database Database name
   * @param string $user Database user
   * @param string $password Database password
   * @param int $port MySQL port
   * @param array $options PDO attributes (http://php.net/manual/de/pdo.setattribute.php)
   *
   * @return PDO
   *
   * @throws Exception
   */
  public static function connect(string $host, string $database, string $user, string $password, int $port = 3306, array $options = []) {

    // custom options
    if (empty($options)) {
      $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "utf8"'
      ];
    }

    try {
      self::$PDO = new PDO("mysql:host={$host};dbname={$database};port={$port}", $user, $password, $options);
    }
    catch (PDOException $e) {
      self::logError($e->getMessage());
      throw new PDOException($e->getMessage(), 1192204);
    }

    return self::$PDO;
  }

  /**
   * Get all query logs
   *
   * @return array
   */
  public function getLog() : array {

    return self::$logs ?? [];
  }

  /**
   * Add last query to log
   *
   * @param string $query MySQL Query
   * @param mixed $result PDO Result
   */
  protected static function addLog(string $query, $result = NULL) {

    if (self::$logging === true) {

      self::$logs[] = [
        "query"  => $query,
        "result" => $result
      ];

    }

  }

  /**
   * Runs prepared statement
   *
   * @param string $query MySQL Query
   * @param array $mapping Data mapping
   *
   * @return PDOStatement
   * @link http://php.net/manual/de/pdo.prepare.php
   *
   */
  public function prepare(string $query, array $mapping = []) : PDOStatement {

    try {
      if (self::$PDO instanceof PDO) {
        $pdoStmnt = self::$PDO->prepare($query);
        $execute  = $pdoStmnt->execute($mapping);
      } else {
        throw new PDOException("The database connection could not be established.", 1121415);
      }
    }
    catch (PDOException $e) {
      self::logError($e->getMessage());
      throw new PDOException($e->getMessage(), 1121413);
    }

    $this->addLog($pdoStmnt->queryString, $execute);

    return $pdoStmnt;
  }

  /**
   * Run a raw mysql query
   *
   * @param string $query
   *
   * @return PDOStatement|false
   */
  public function query(string $query) {

    try {
      if (self::$PDO instanceof PDO) {
        $queryObj = self::$PDO->query($query);
      } else {
        throw new PDOException("The database connection could not be established.", 1121501);
      }
    }
    catch (PDOException $e) {
      self::logError($e->getMessage());
      throw new PDOException($e->getMessage(), 1121011);
    }

    $this->addLog($query, gettype($queryObj));

    if ($queryObj instanceof PDOStatement) {
      return $queryObj;
    }

    return false;
  }

  /**
   * Execute raw mysql query
   *
   * @param string $query
   *
   * @return void
   */
  public function execute(string $query) : void {

    try {
      if (self::$PDO instanceof PDO) {
        self::$PDO->exec($query);
      } else {
        throw new PDOException("The database connection could not be established.", 1121519);
      }
    }
    catch (PDOException $e) {
      self::logError($e->getMessage());
      throw new PDOException($e->getMessage(), 1121516);
    }

    $this->addLog($query);
  }

  /**
   * Check table exists in mysql
   *
   * @param string $table
   *
   * @return bool
   */
  public function isTable(string $table) : bool {

    try {
      $pdoStmnt = $this->query(sprintf("DESCRIBE `%s`", $table));
    }
    catch (PDOException $e) {
      self::logError($e->getMessage());
      throw new PDOException($e->getMessage(), 1121523);
    }

    if ($pdoStmnt instanceof PDOStatement) {
      return $pdoStmnt->rowCount() > 0;
    }

    return false;
  }

  /**
   * @param $function
   * @param $sql
   * @param $map
   *
   * @return bool|int|mixed|null
   * @throws Exception
   */
  private function load($function, $sql, $map) {
    $data = NULL;

    // run MySQL query
    $stmt = $this->prepare($sql, $map);

    // call result method
    if ($stmt instanceof PDOStatement) {

      switch ($function) {
        case "rowCount":
          $data = $stmt->rowCount();
          break;
        default:
          if (method_exists($stmt, $function)) {
            $data = $stmt->{$function}(PDO::FETCH_ASSOC);
          } else {
            throw new Exception("Class DB: missing method: {$function}");
          }
      }

      return $data;
    }

    return false;
  }

  /**
   * @param string $sql
   * @param array $map
   * @param mixed $default
   *
   * @return bool|array
   * @throws Exception
   */
  public function fetch(string $sql, array $map = [], $default = []) : ?array {
    $data = $this->load("fetch", $sql, $map);

    return empty($data) ? $default : $data;
  }

  /**
   * @param string $sql
   * @param array $map
   * @param mixed $default
   *
   * @return bool|array
   * @throws Exception
   */
  public function fetchAll(string $sql, array $map = [], $default = []) : ?array {
    $data = $this->load("fetchAll", $sql, $map);

    return empty($data) ? $default : $data;
  }

  /**
   * @param string $sql
   * @param array $map
   *
   * @return int
   * @throws Exception
   */
  public function rowCount(string $sql, array $map = []) : int {
    return (int)$this->load("rowCount", $sql, $map);
  }

}
