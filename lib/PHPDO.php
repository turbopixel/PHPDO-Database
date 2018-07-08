<?php

namespace PHPDO;

use PDO;
use PDOException;
use PDOStatement;

/**
 * PHPDO
 * A lightweight PHP PDO database wrapper class for everyone.
 *
 * @author Nico Hemkes
 */
class PHPDO {

  /**
   * @var \PDO
   */
  protected $PDO;

  /**
   * @var array
   */
  protected $logs = [];

  /**
   * Log queries
   * @var bool
   */
  public $logging = false;

  /**
   * Get pdo object
   *
   * @return PDO
   *
   * @throws \Exception
   */
  public function getPdo() : \PDO {

    if ($this->PDO instanceof \PDO) {
      return $this->PDO;
    }

    throw new \Exception("PDO object lost", 1523905129030);
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
   */
  public function connect(string $host, string $database, string $user, string $password, int $port = 3306, array $options = []) {

    // custom options
    if (!empty($options)) {

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
      die("PHPDO: " . $e->getCode() . ": " . $e->getMessage());
    }

    // set PHPDO instance
    DB::setInstance($this);
  }

  /**
   * Add last query to log
   *
   * @param string $query MySQL Query
   * @param mixed $result PDO Result
   */
  protected function addLog(string $query, $result) {

    if ($this->logging === true) {

      $this->logs[] = [
        "query"  => $query,
        "result" => $result
      ];

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
   * @return string
   */
  public function getLastQuery() {

    return end($this->logs);
  }

  /**
   * Runs raw mysql query
   *
   * @param string $query
   *
   * @return PDOStatement
   */
  public function query(string $query) {

    try {
      $queryObj = $this->PDO->query($query);
    }
    catch (PDOException $e) {
      die("error on line " . __LINE__ . ": " . $e->getCode());
    }

    $this->addLog($query, gettype($queryObj));

    return $queryObj;
  }

  /**
   * Runs prepared statement
   *
   * @param string $query MySQL Query
   * @param array $mapping Data mapping
   *
   * @link http://php.net/manual/de/pdo.prepare.php
   *
   * @return PDOStatement
   */
  public function prepare(string $query, array $mapping = []) : PDOStatement {

    try {
      $pdoStmnt = $this->PDO->prepare($query);
      $execute  = $pdoStmnt->execute($mapping);
    }
    catch (PDOException $e) {
      die("error on line " . __LINE__ . ": " . $e->getCode());
    }

    $this->addLog($pdoStmnt->queryString, $execute);

    return $pdoStmnt;
  }

  /**
   * Execute raw mysql query
   *
   * @param string $query
   *
   * @return int
   */
  public function execute(string $query) : int {

    try {
      $exec = $this->PDO->exec($query);
    }
    catch (PDOException $e) {
      die("error on line " . __LINE__ . ": " . $e->getCode());
    }

    $this->addLog($query, $exec);

    return $exec;
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
      die("error on line " . __LINE__ . ": " . $e->getCode());
    }

    if ($pdoStmnt instanceof PDOStatement) {
      return $pdoStmnt->rowCount() > 0;
    }

    return false;
  }

}