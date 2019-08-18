# PHPDO

A lightweight php pdo database wrapper class.

Docs and examples: [phpdo.hemk.es](http://phpdo.hemk.es)

#### composer

```
composer require turbopixel/phpdo-database
```

## Using PHPDO

#### Database connect
```php
$PHPDO = new PHPDO();
$PHPDO->connect("database-server.com", "database_name", "user_name", "myPassword123");
```

---

#### Get PDO Instance
```php
$PHPDO->getPdo();
```
use the class DB
```php
DB::getInstance()->query("SELECT * FROM user")->fetchAll();
```

---

#### Execute SQL Queries

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
$PHPDO->prepare("SELECT id FROM user WHERE id = :userid", ["userid" => 553]);
```

---

#### Helper

**Check table exists (MySQL only)**
```php
$PHPDO->isTable("user_settings")
```

---

### Internal class logging

All sqls stored in PHPDO::$logs

**Get internal query logs**  
Get query logs. Attribute $PHPDO->logging must be true!
```php
$PHPDO->getLog(); // returns an array
```

**Get last query log**  
Class attribute $PHPDO->logging must be true!
```php
$PHPDO->getLastQuery(); // returns last sql as array
```
