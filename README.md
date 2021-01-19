# PHPDO

![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/turbopixel/PHPDO-Database)
[![MIT license](https://img.shields.io/badge/License-MIT-blue.svg)](https://github.com/turbopixel/PHPDO-Database/blob/master/LICENSE)
![GitHub contributors](https://img.shields.io/github/contributors/turbopixel/PHPDO-Database)
![GitHub last commit](https://img.shields.io/github/last-commit/turbopixel/PHPDO-Database)
[![packagist](https://badgen.net/packagist/v/turbopixel/PHPDO-Database)](https://packagist.org/packages/turbopixel/phpdo-database)

A lightweight PHP7 PDO database singleton wrapper class.

Docs and examples: [github.com/turbopixel/PHPDO-Database](https://github.com/turbopixel/PHPDO-Database)

### Requirements

* PHP 7.2+
* MySQL/MariaDB

### Install via composer

```text
composer require turbopixel/phpdo-database
```

## Example

```php
PHPDO::connect("database-server.com", "database_name", "user_name", "myPassword123");

PHPDO::get()->query("SELECT stars FROM github")->fetchAll();
```

## class PHPDO

### Create database connection

```php
\PHPDO\PHPDO::connect("database-server.com", "database_name", "user_name", "myPassword123");
```

After this, you can use the PHPDO class from everywhere.

## Get instance

**\PHPDO\PHPDO::get()** returns the PHPDO instance

```php
\PHPDO\PHPDO::get()
```

Example: Select rows

```php
\PHPDO\PHPDO::get()->query("SELECT * FROM github")->fetchAll();
```

**Get PDO instance**

```php
\PHPDO\PHPDO::get()->getPdo()
```

## Run MySQL query

**query**

```php
\PHPDO\PHPDO::get()->query("SELECT id FROM user WHERE active = 1");
print_r( $pdoStmnt->fetch() );
```

**execute**

```php
\PHPDO\PHPDO::get()->execute("UPDATE user SET active = 0 WHERE mail IS NULL");
```

**Prepared Statement**

```php
\PHPDO\PHPDO::get()->prepare("UPDATE github SET stars = stars+1 WHERE id = :id", ["id" => 1234]);
```

## Helper

### fetch() - Select a single row

```php
\PHPDO\PHPDO::get()->fetch("SELECT id FROM github WHERE id = :repo", ["repo" => 553]);
```

`\PHPDO\PHPDO::get()->fetch()` is a helper method and replace this:

```php
$rows  = [];
$stmnt = \PHPDO\PHPDO::get()->prepare("SELECT * FROM github WHERE id = ?", [
  1234
]);

if($stmnt instanceof PDOStatement){
  $rows = $stmnt->fetchAll();
}else{
 die("QUERY ERROR");
}

print_r($rows);
```

### fetchAll() - Select multiple rows

```php
\PHPDO\PHPDO::get()->fetchAll("SELECT id FROM github WHERE id = :repo", ["repo" => 553]);
```

### rowCount() - Count rows

```php
\PHPDO\PHPDO::get()->rowCount("SELECT id FROM github WHERE id = :repo", ["repo" => 553]);
```

### isTable() - Check table exists (MySQL only)

```php
\PHPDO\PHPDO::get()->isTable("user_settings")
```

## Internal class logging

All SQL Queries stored in PHPDO::$logs (array). Attribute `\PHPDO\PHPDO::$logging` must be `true`

**Enable logging**

```php
\PHPDO\PHPDO::$logging = true;
```

**Get internal query logs**  
Get query logs.

```php
\PHPDO\PHPDO::get()->getLog(); // returns an array
```