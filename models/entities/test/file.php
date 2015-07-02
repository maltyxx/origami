<?php
namespace Entity\test;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class file extends \Origami\Entity {
    
	public static $table = 'file';
    
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property string $street
	 */
	public static $fields = array(
		array('name' => 'id', 'type' => 'int'),
		array('name' => 'user_id', 'type' => 'int'),
		array('name' => 'type', 'type' => 'string'),
		array('name' => 'content', 'type' => 'string', 'binary' => TRUE)
	);
    
	public static $primary_key = 'id';
    
	public static $associations = array(
		array('association_key' => 'user', 'entity' => '\Entity\test\user', 'type' => 'has_one', 'primary_key' => 'id', 'foreign_key' => 'user_id')
	);
}