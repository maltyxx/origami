<?php
namespace Entity\test;

defined('BASEPATH') OR exit('No direct script access allowed');

class user extends \Origami\Entity {

	public static $table = 'user';

	/**
	 * @property integer $id
	 * @property string $lastname
	 * @property string $firstname
	 * @property string $password
	 */
	public static $fields = array(
		array('name' => 'id', 'type' => 'int'),
		array('name' => 'lastname', 'type' => 'string'),
		array('name' => 'firstname', 'type' => 'string'),
		array('name' => 'password', 'type' => 'string'),
		array('name' => 'birth', 'type' => 'date', 'date_format' => 'Y-m-d'),
		array('name' => 'dateinsert', 'type' => 'date'),
		array('name' => 'dateupdate', 'type' => 'date')
	);

	public static $primary_key = 'id';
    
	public static $associations = array(
		array('association_key' => 'address', 'entity' => '\Entity\test\address', 'type' => 'has_many', 'primary_key' => 'id', 'foreign_key' => 'user_id')
	);
}

