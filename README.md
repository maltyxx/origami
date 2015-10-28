# Origami ORM
Object Relational Mapping for Codeigniter 3

Origami ORM was build for CodeIgniter3 application, it's a programming technique for converting data between 
incompatible type systems in object-oriented programming languages.

#####**WHY USE ORIGAMI ORM ?**

  * **Security** : 
      Origami can encoding file in base64 and encrypting adress (certified eTrust).
  
  * **Lighter than others** :
      Origami only use essential package.

  * **Friendly use** :
      Easy and better understand.
      

  * **Model Generator** :
      Model Generator is a controller who create automatically your entity, you don't need to create them manually

----------

| Documentation|
|-------------| ----------- |
| Requirements |
| Installation |
| Schema |
| CRUD |
| Query Builder |
| Transactions |
| Security |
----------

## Requirements

- PHP 5.4.x (Composer requirement)
- CodeIgniter 3.0.x

## Installation
### Installation by Composer
#### Run composer
```shell
composer require maltyxx/origami
```

### Create files
Create controller file in `/application/controllers/Origami_generator.php`.
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'/third_party/origami/controllers/Origami_generator.php');
```

### Configuration
Duplicate configuration file `./application/third_party/origami/config/origami.php` in `./application/config/origami.php`.

## Schema

###- *Table*
```php
namespace Entity\Database;

defined('BASEPATH') OR exit('No direct script access allowed');

class table extends \Origami\Entity
{	
    public static $table = 'database';
}
```
###- *Primary key*
```php
namespace Entity\Database;

defined('BASEPATH') OR exit('No direct script access allowed');

class table extends \Origami\Entity
{
    public static $primary_key = 'id';
}
```
###- **Field**
```php
namespace Entity\Database;

defined('BASEPATH') OR exit('No direct script access allowed');

class table extends \Origami\Entity
{
    public static $fields = array(
    	array('field' => 'birthday', 'type' => 'date', 'date_format' => 'Y-m-d H:i:s'),
    	array('field' => 'key', 'type' => 'int', 'encrypt' => TRUE),
    	array('field' => 'street', 'type' => 'string', 'allow_null' => TRUE),
    	array('field' => 'content', 'type' => 'string', 'binary' => TRUE)
    );
}
```
###- **Validation**

```php
namespace Entity\Database;

defined('BASEPATH') OR exit('No direct script access allowed');

class table extends \Origami\Entity
{
    public static $validations = array(
    	array('field' => 'id', 'type' => 'present'),
        array('field' => 'age', 'type' => 'int', 'message' => "Your age is required"),
        array('field' => 'email', 'type' => 'email'),
    	array('field' => 'ip', 'type'=> 'ip'),
    	array('field' => 'price', 'type'=> 'float'),
    	array('field' => 'status', 'type'=> 'inclusion', list => [0, 1]),
    	array('field' => 'type', 'type'=> 'format', 'matcher' => '^(man|woman)$'),
    	array('field' => 'birthday', 'type'=> 'callback' => 'self::checkdate'),
    );

    public static function checkdate($value)
    {
        return (date('Y-m-d', strtotime($value)) === $value)
    }
}
```
###- **Association**

| Association Schema|       |
|-------------| ----------- |
| HAS_ONE | BELONGS_TO |
| HAS_MANY |           |
---------------------------

```php
namespace Entity\Database;

defined('BASEPATH') OR exit('No direct script access allowed');

class table extends \Origami\Entity
{
    public static $associations = array(
    	array(
            'association_key' => 'address',
            'entity' => '\Entity\test\address',
            'type' => 'has_many',
            'primary_key' => 'id',
            'foreign_key' => 'user_id'
        )
    );
}
	
```

## CRUD

###- *Add*
**first, create new entity :**
```php
$user = new \Entity\test\user();
```
**And add value :**
```php
$user->firstname = 'John';
$user->lastname = 'Do';
```
**Then save it !**
```php
$user->save();
```
###- *Get*
**The** `get()` **function allow you to get a request in the database, 
with Origami you have to use** `find_one()` **:**
```php
$table = \Entity\test\table::where('id', 10)->find_one();
```
**find_one search inside the first tab found**
```php
$table = new \Entity\test\table(10);
```
###- *Edit*
**The** `set()` **function using** `find_one()` **to find the first value :**
```php
$table= \Entity\database\table::where('username', 'john')->find_one();
```
**Or if you want make a specification :**
```php
$table->username = 'Smith';
$table->save();
```

###- *Delete*
**You can delete a value with** `remove()` **:**
```php
$table = \Entity\test\table::where('id', '134')->find_one();
return $table->remove();
```

# Query Builder
**Origame use Query Builder Class of code igniter 3 :**

| Query Builder Origami| 
|----------------------|
| `group_start()`|
| `or_group_start()`|
| `not_group_start()`|
| `or_not_group_start()`|
| `or_where()`|          
| `where_in()`|
| `or_where_in()`|
| `where_not_in()`|
| `or_where_not_in()`|
| `like()`|
| `or_like()`|
| `not_like()`|
| `or_not_like()`|
| `group_by()`|
| `having()`|
| `or_having()`|
| `order_by()`|
| `limit()`|
| `join()`|
| `offset()`|
| `select()`|
| `find()`|
| `find_one()`|
| `result()`|
| `count()` |
| `delete()`|
-------------------------------
**group_start / or_group_start :**
```php
$table = \Entity\database\table::
    ->group_start()
    ->where('table1', 'table1')
    ->or_group_start()
    ->where('table2', 'table2')
    ->group_end()
    ->where('table3', 'table3')
    ->find();
```
```sql
SELECT * FROM (`table`) WHERE ( `table1` = 'table1' OR ( `table2` = 'table2') ) AND `table3` = 'table3'
```
**where / or_where :**
```php
$table = \Entity\database\table::
    ->where('field !=', $field)
    ->or_where('id >', $id)
    ->find();
```
```sql
WHERE field != 'table1' OR id > 5
```
**where_in :**
```php
$field = array('table1', 'table2', 'table3');
$table = \Entity\database\table::
    ->where_in('field', $field)
    ->find();
```
```sql
WHERE field IN ('table1', 'table2', 'table3')
```

**or_where_in :**
```php
$field = array('table1', 'table2', 'table3');
$table = \Entity\database\table::
    ->or_where_in('field', $field)
    ->find();
```
```sql
OR field IN ('table1', 'table2', 'table3')
```

**where_not_in :**
```php
$field = array('table1', 'table2', 'table3');
$table = \Entity\database\table::
    ->where_not_in('field', $field)
    ->find();
```
```sql
WHERE field NOT IN ('table1', 'table2', 'table3')
```
**like :**
```php
$table = \Entity\database\table::
    ->like('field' , 'table1')
    ->find();
```
```sql
WHERE `group_name` LIKE '%table1%'
```
**or_like :**
```php
$table = new \Entity\database\table::
    ->like('field', 'table1')
    ->or_like('field', 'table2')
    ->find();
```
```sql
WHERE `field` LIKE '%table1%' OR  `field` LIKE '%table2%'
```
**not_like :**
```php
$table = new \Entity\database\table::
    ->not_like('field' , 'table1')
    ->find();
```
```sql
WHERE `field` NOT LIKE '%grp1%'
```
**or_not_like :**
```php
$table = \Entity\database\table::
    ->like('field' , 'table1')
    ->or_not_like('field' , 'table2')
    ->find();
```
```sql
WHERE `field` LIKE '%table1%' OR  `field` NOT LIKE '%table2%'
```
**group_by :**
```php
$table = \Entity\database\table::
    ->group_by('field')
    ->find();
```
```sql
GROUP BY field
```
**having**
```php
$table = \Entity\database\table::
    ->having('table_id', 22)
    ->find();
```
```sql
HAVING table_id = 22
```
**or_having :**
```php
$table = \Entity\database\table::
    ->having('table_id', 22)
    ->or_having('table_id', 23)
    ->find();
```
```sql
HAVING table_id = 22 OR table_id = 23
```
**limit :**
```php
$table = \Entity\database\table::
    ->limit(30)
    ->find();
```
```sql
LIMIT 30
```
**delete :**
```php
$table = \Entity\database\table::
    ->where('id', '5')
    ->delete($table)
```
```sql
DELETE FROM table WHERE id = 5
```

## Transactions

**Transactions can be used when you want to make sure all the statements you specify are executed.
If at least one statement fails, all the changes will be rolled back and 
database will be in its initial state.**

```php
\Origami\DB::get('database')->trans_start();
 
$table1 = new \Entity\database\table1();
$table1->username = 'Adrien';
$table1->save();
   
$table2 = new \Entity\database\table2();
$table2->table1_id = $table1->id;
$table2->save();
  
\Origami\DB::get('database')->trans_complete();
  
if (\Origami\DB::get('database')->trans_status()) {
    // TRUE
} else {
    // FALSE
}
```

## Security

### -*Encryption*
**You can encrypt a string with origami but before, you need to activate it in** *application/origami/config/origami.php*
```php
$config['origami'] = array(
    'encryption_enable' => TRUE,
    'encryption_key' => bin2hex('Origami')
);
```
**username is automatically encoded during the insertion and decoded when you use it**
```php
$table = new \Entity\database\table();
$table->username = 'Adrien';
$table->save();
```

### -*Encoding*
**File :** *application/origami/config/origami.php*
```php
$config['origami'] = array(
    'binary_enable' => TRUE
);
```
**file_content is a binary file automatically encoded in base 64 during the insertion and decoded when you use it**
```php
$table = new \Entity\database\table();
$table->file_content = base64_encode(file_get_contents('https://www.google.fr/images/srpr/logo11w.png'));
$table->save();
```
--------------------------
