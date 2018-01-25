<?php

namespace PHPDO;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

/**
 * @author Nico Hemkes
 */
class PHPDO {

  /**
   * @var PDO
   */
  protected $PDO;

  /**
   * Create database connection
   *
   * @param string $host Hostname like localhost or 127.0.0.1
   * @param string $user Database user
   * @param string $password Database password
   * @param string $database Databasename
   * @param int $port MySQL port
   */
  public function connect(string $host, string $database, string $user, string $password, int $port = 3306) {

    $opt = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false
    ];

    try {
      $this->PDO = new PDO("mysql:host={$host};dbname={$database};port={$port}", $user, $password, $opt);
    }
    catch (PDOException $e) {
      die($e->getMessage());
    }

  }

  /**
   * Runs prepared statement
   *
   * @param string $query SQL Query
   * @param array $mapping Column mapping
   *
   * @link http://php.net/manual/de/pdo.prepare.php
   *
   * @return PDOStatement
   */
  public function prepare(string $query, array $mapping) : PDOStatement {

    $pdoStmnt = $this->PDO->prepare($query);
    $pdoStmnt->execute($mapping);

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

    return $this->PDO->exec($query);
  }

  /**
   * Insert data into table
   *
   * @param string $table Tablename
   * @param array $columns [Column => Value] array
   *
   * @return string
   * @throws Exception
   */
  public function insert(string $table, array $columns) : string {

    $columns         = array_unique($columns);
    $preparedColumns = [];
    $columnName      = [];

    foreach ($columns AS $key => $value) {
      $columnName[]      = $key;
      $preparedColumns[] = $value;
    }

    $columnList  = implode(",", $columnName);
    $valuePrefix = rtrim(str_repeat("?,", count($columnName)), ",");

    $query = "INSERT INTO {$table} ({$columnList}) VALUES ({$valuePrefix})";
    $this->prepare($query, $preparedColumns);

    return $this->PDO->lastInsertId();
  }

  /**
   * Run query and return row count
   *
   * @param string $query
   * @param array $mapping
   *
   * @return int
   */
  public function count(string $query, array $mapping = []) : int {

    $pdoStmnt = $this->prepare($query, $mapping);

    return $pdoStmnt->rowCount();
  }

}