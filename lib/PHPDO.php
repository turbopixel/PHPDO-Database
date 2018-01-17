<?php

namespace PHPDO;

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
   * Execute query
   *
   * @param string $query
   *
   * @return int
   */
  public function execute(string $query) : int{

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
  public function prepare(string $query, array $mapping) :  PDOStatement{

    $pdoStmnt = $this->PDO->prepare($query);
    $pdoStmnt->execute($mapping);

    return $pdoStmnt;
  }

}