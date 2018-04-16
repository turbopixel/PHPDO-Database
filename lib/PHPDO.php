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
   * @var PDO
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
   * Create database connection
   *
   * @param string $host Hostname
   * @param string $database Database name
   * @param string $user Database user
   * @param string $password Database password
   * @param int $port MySQL port
   */
  public function connect(string $host, string $database, string $user, string $password, int $port = 3306) {

    $opt = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "utf8"'
    ];

    try {
      $this->PDO = new PDO("mysql:host={$host};dbname={$database};port={$port}", $user, $password, $opt);
    }
    catch (PDOException $e) {
      die($e->getMessage());
    }

  }

  /**
   * Add last query to log
   *
   * @param string $query
   */
  protected function addLog(string $query) {
    if ($this->logging === true) {
      $this->logs[] = $query;
    }
  }

  /**
   * Get query logs
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
   * Runs prepared statement
   *
   * @param string $query MySQL Query
   * @param array $mapping Data mapping
   *
   * @link http://php.net/manual/de/pdo.prepare.php
   *
   * @return PDOStatement
   */
  public function prepare(string $query, array $mapping) : PDOStatement {
    $pdoStmnt = $this->PDO->prepare($query);
    $pdoStmnt->execute($mapping);

    $this->addLog($pdoStmnt->queryString);

    return $pdoStmnt;
  }

  /**
   * Execute raw query
   *
   * @param string $query
   *
   * @return int
   */
  public function execute(string $query) : int {
    $this->addLog(query);

    return $this->PDO->exec($query);
  }

}