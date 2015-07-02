<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['origami'] = array(
    'entity_autoload' => TRUE,
    'entity_path' => APPPATH.'third_party/origami/models/entities',
    'binary_enable' => TRUE,
    'encryption_enable' => TRUE,
    'encryption_key' => bin2hex('Origamiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii')
);