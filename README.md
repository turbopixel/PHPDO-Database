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

```phpregexp
$PHPDO = new PHPDO();
$PHPDO->connect("database-server.com", "database_name", "user_name", "myPassword123");

$PHPDO->query("SELECT stars FROM github")->fetchAll();
```

## class PHPDO

### Create database connection

```phpregexp
$PHPDO = new PHPDO();
$PHPDO->connect("database-server.com", "database_name", "user_name", "myPassword123");
```

After this, you can use the PHPDO class from everywhere.

## Get instance

**\PHPDO\PHPDO::get()** returns the PHPDO instance

```phpregexp
\PHPDO\PHPDO::get()
```

Example: Run a query

```phpregexp
\PHPDO\PHPDO::get()->query("SELECT * FROM github")->fetchAll();
```

**Get PDO instance**

```phpregexp
\PHPDO\PHPDO::get()->getPdo()
```

## Run MySQL query

**query**

```phpregexp
$PHPDO->query("SELECT id FROM user WHERE active = 1");
print_r( $pdoStmnt->fetch() );
```

**execute**

```phpregexp
$PHPDO->execute("UPDATE user SET active = 0 WHERE mail IS NULL");
```

**Prepared Statement**

```phpregexp
$PHPDO->prepare("UPDATE github SET stars = stars+1 WHERE id = :id", ["id" => 1234]);
```

## Helper

### fetch() - Select a single row

```phpregexp
$PHPDO->fetch("SELECT id FROM github WHERE id = :repo", ["repo" => 553]);
```

`$PHPDO->fetch()` is a helper method and replace this:

```phpregexp
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

```phpregexp
$PHPDO->fetchAll("SELECT id FROM github WHERE id = :repo", ["repo" => 553]);
```

### rowCount() - Count rows

```phpregexp
$PHPDO->rowCount("SELECT id FROM github WHERE id = :repo", ["repo" => 553]);
```

### Check table exists (MySQL only)

```phpregexp
$PHPDO->isTable("user_settings")
```

## Internal class logging

All SQL Queries stored in PHPDO::$logs (array)

**Enable logging**

```phpregexpregexp
$PHPDO->logging = true
```

**Get internal query logs**  
Get query logs. Attribute `$PHPDO->logging` must be set `true`

```phpregexp
$PHPDO->getLog(); // returns an array
```

**Get last query log**  
Class attribute $PHPDO->logging must be true!

```phpregexp
$PHPDO->getLastQuery(); // returns last sql as array
```
