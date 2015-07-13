# Origami ORM
Object Relational Mapping for Codeigniter 3

## Installation
### Step 1 Installation by Composer
#### Edit /composer.json
```json
{
    "require": {
        "maltyxx/origami": "dev-master"
    }
}
```
#### Run composer update
```shell
composer update
```
### Step 2 Create files
```txt
/application/controllers/origami_generator.php for CodeIgniter 2
/application/controllers/Origami_generator.php for CodeIgniter 3
```
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'/third_party/origami/controllers/Origami_generator.php');
```