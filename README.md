# Origami ORM
Object Relational Mapping for Codeigniter 3

## Requirements

- PHP 5.4.x (Composer requirement)
- CodeIgniter 3.0.x

## Installation
### Step 1 Installation by Composer
#### Run composer
```shell
composer require maltyxx/origami
```

### Step 2 Create files
Create controller file in `/application/controllers/Origami_generator.php`.
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'/third_party/origami/controllers/Origami_generator.php');
```

### Step 3 Configuration
Duplicate configuration file `./application/third_party/origami/config/origami.php` in `./application/config/origami.php`.

### Step 4 Examples
Model file is located in `/application/models/User_model.php`.
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();

       $this->load->add_package_path(APPPATH.'third_party/origami');
       $this->load->library('origami');
       $this->load->remove_package_path(APPPATH.'third_party/origami');
    }
    
    public function create()
    {
        $user = new \Entity\test\user();
        $user->firstname = 'John';
        $user->lastname = 'Do';
        $user->dateinsert = new DateTime();
        $user->dateupdate = new DateTime();
        $user->save();
    }
}
```
