<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['entity_autoload'] = TRUE;
$config['entity_path'] = APPPATH.'third_party/origami/models/Entity';
$config['binary_enable'] = TRUE;
$config['encryption_enable'] = TRUE;
$config['encryption_key'] = bin2hex('Origami');