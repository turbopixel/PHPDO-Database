# PHPDO

A lightweight PHP7 PDO database wrapper class.

Docs and examples: [github.com/turbopixel/PHPDO-Database](https://github.com/turbopixel/PHPDO-Database)

### Requirements

* PHP 7.2
* MySQL/MariaDB

### Install via composer

```text
composer require turbopixel/phpdo-database
```

## Example

```php
$PHPDO = new PHPDO();
$PHPDO->connect("database-server.com", "database_name", "user_name", "myPassword123");

$PHPDO->query("SELECT stars FROM github")->fetchAll();
```

## class PHPDO

### Create database connection

```php
$PHPDO = new PHPDO();
$PHPDO->connect("database-server.com", "database_name", "user_name", "myPassword123");
```

After this, you can use the PHPDO class from everywhere.

## Get instance

**\PHPDO\PHPDO::get()** returns the PHPDO instance

```php
\PHPDO\PHPDO::get()
```

Example: Run a query

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
$PHPDO->query("SELECT id FROM user WHERE active = 1");
print_r( $pdoStmnt->fetch() );
```

**execute**

```php
$PHPDO->execute("UPDATE user SET active = 0 WHERE mail IS NULL");
```

**Prepared Statement**

```php
$PHPDO->prepare("UPDATE github SET stars = stars+1 WHERE id = :id", ["id" => 1234]);
```

## Helper

### fetch() - Select a single row

```php
$PHPDO->fetch("SELECT id FROM github WHERE id = :repo", ["repo" => 553]);
```

`$PHPDO->fetch()` is a helper method and replace this:

```php
$rows  = [];
$stmnt = $PHPDO->prepare("SELECT * FROM github WHERE id = ?", [
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
$PHPDO->fetchAll("SELECT id FROM github WHERE id = :repo", ["repo" => 553]);
```

### rowCount() - Count rows

```php
$PHPDO->rowCount("SELECT id FROM github WHERE id = :repo", ["repo" => 553]);
```

### Check table exists (MySQL only)

```php
$PHPDO->isTable("user_settings")
```

## Internal class logging

All SQL Queries stored in PHPDO::$logs (array)

**Enable logging**

```phpregexp
$PHPDO->logging = true
```

**Get internal query logs**  
Get query logs. Attribute `$PHPDO->logging` must be set `true`

```php
$PHPDO->getLog(); // returns an array
```

**Get last query log**  
Class attribute $PHPDO->logging must be true!

```php
$PHPDO->getLastQuery(); // returns last sql as array
```
