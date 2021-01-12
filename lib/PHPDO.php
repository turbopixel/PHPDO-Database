<?php

namespace PHPDO;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

/**
 * PHPDO
 * A lightweight PHP PDO database wrapper class for everyone
 * with internal log stash
 *
 * @author Nico Hemkes
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
  public $logging = false;
  /**
   * @var PDO
   */
  protected $PDO;

  /**
   * @var array
   */
  protected $logs = [];

  /**
   * PHPDO constructor.
   */
  public function __construct() {
    self::$_instance = $this;

    $this->checkPhpVersion();
  }

  /**
   * PHP version check
   *
   * @throws Exception
   */
  private function checkPhpVersion() {

    if (PHP_MAJOR_VERSION >= 7 && PHP_MINOR_VERSION >= 2 !== true) {
      $this->logError('php version must be 7.2 or higher');
      throw new Exception('php version must be 7.2 or higher');
    }

  }

  /**
   * Internal php error log
   *
   * @param string $message
   */
  private function logError(string $message) {

    if (function_exists('error_log')) {
      error_log($message);
    }

  }

  /**
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

    if ($this->PDO instanceof PDO) {
      return $this->PDO;
    }

    throw new Exception("PDO object lost", 1523905129030);
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
   * @throws Exception
   */
  public function connect(string $host, string $database, string $user, string $password, int $port = 3306, array $options = []) {

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
      $this->PDO = new PDO("mysql:host={$host};dbname={$database};port={$port}", $user, $password, $options);
    }
    catch (PDOException $e) {
      $this->logError($e->getMessage());
      throw new PDOException($e->getMessage(), 1534363929089);
    }

  }

  /**
   * Get all query logs
   *
   * @return array
   */
  public function getLog() : array {

    return $this->logs ?? [];
  }

  /**
   * Returns the last query
   *
   * @return string|null
   */
  public function getLastQuery() : ?string {

    if (empty($this->logs)) {
      return NULL;
    }

    return end($this->logs);
  }

  /**
   * Add last query to log
   *
   * @param string $query MySQL Query
   * @param mixed $result PDO Result
   */
  protected function addLog(string $query, $result = NULL) {

    if ($this->logging === true) {

      $this->logs[] = [
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
      $pdoStmnt = $this->PDO->prepare($query);
      $execute  = $pdoStmnt->execute($mapping);
    }
    catch (PDOException $e) {
      $this->logError($e->getMessage());
      throw new PDOException($e->getMessage(), 1534363953164);
    }

    $this->addLog($pdoStmnt->queryString, $execute);

    return $pdoStmnt;
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
      $this->PDO->exec($query);
    }
    catch (PDOException $e) {
      $this->logError($e->getMessage());
      throw new PDOException($e->getMessage(), 1534363963076);
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
      $this->logError($e->getMessage());
      throw new PDOException($e->getMessage(), 1534363969842);
    }

    if ($pdoStmnt instanceof PDOStatement) {
      return $pdoStmnt->rowCount() > 0;
    }

    return false;
  }

  /**
   * Runs raw mysql query
   *
   * @param string $query
   *
   * @return PDOStatement|false
   */
  public function query(string $query) {

    try {
      $queryObj = $this->PDO->query($query);
    }
    catch (PDOException $e) {
      $this->logError($e->getMessage());
      throw new PDOException($e->getMessage(), 1534363955802);
    }

    $this->addLog($query, gettype($queryObj));

    if ($queryObj instanceof PDOStatement) {
      return $queryObj;
    }

    return false;
  }

}
