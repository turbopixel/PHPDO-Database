<?php

namespace PHPDO;

use PDO;
use PDOException;

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
  public function connect(string $host, int $port, string $user, string $password, string $database) {

    try {
      $this->PDO = new PDO("mysql:host={$host};dbname={$database};port={$port}", $user, $password);
    }
    catch (PDOException $e) {
      die($e->getMessage());
    }

  }

}