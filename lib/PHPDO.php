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
   * @param int $port MySQL port
   * @param string $user Database user
   * @param string $password Database password
   * @param string $database Databasename
   */
  public function connect(string $host, int $port = 3306, string $user, string $password, string $database) {

    try {
      $this->PDO = new PDO("mysql:host={$host};dbname={$database};port={$port}", $user, $password);
    }
    catch (PDOException $e) {
      die($e->getMessage());
    }

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
   * Insert data into table
   *
   * @param string $table Tablename
   * @param array $columns Column->Value array
   *
   * @return string
   * @throws Exception
   */
  public function insert(string $table, array $columns) : string {

    if (empty($table)) {
      throw new Exception("unknown table");
    }

    if (empty($columns)) {
      throw new Exception("unknown mapping");
    }

    $columns = array_unique($columns);

    foreach($columns AS $key => $value){

      $columnArr[] = $key;
      $valueArr[] = "'{$value}'";

    }

    $query = "INSERT INTO {$table} (" . implode(", ", $columnArr) . ") VALUES (" . implode(", ", $valueArr) . ")";
    $this->PDO->query($query);

    return $this->PDO->lastInsertId();
  }

}