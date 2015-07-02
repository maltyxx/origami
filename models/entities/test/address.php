<?php
namespace Entity\test;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class address extends \Origami\Entity {
    
	public static $table = 'address';
    
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property string $street
	 */
	public static $fields = array(
		array('name' => 'id', 'type' => 'int'),
		array('name' => 'user_id', 'type' => 'int'),
		array('name' => 'street', 'type' => 'string', 'allow_null' => true, 'encrypt' => TRUE),
		array('name' => 'vector', 'type' => 'string', 'allow_null' => true)
	);
    
	public static $primary_key = 'id';
    
	public static $associations = array(
		array('association_key' => 'user', 'entity' => '\Entity\test\user', 'type' => 'belongs_to', 'primary_key' => 'id', 'foreign_key' => 'user_id')
	);
}