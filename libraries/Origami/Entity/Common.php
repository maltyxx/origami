<?php

namespace Origami;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Common
{
    /**
     * Nom de la table
     * @var string $table
     */
	protected static $table;
	
    /**
     * Nom de la clé primaire
     * @var string $primary_key
     */
	protected static $primary_key;
	
    /**
     * Les champs
     * @var array $fields
     */
	protected static $fields = array();
	
    /**
     * Les associations
     * @var array $associations
     */
	protected static $associations = array();
	
    /**
     * Les validations
     * @var array $validations
     */
	protected static $validations = array();
	
    /**
     * 
     * @var \Origami\Entity\Config $config
     */
	protected static $config;
	
	public static function __callStatic($name, $arguments = array()) {
		        
        if (!isset(self::$config[self::entity()])) {
            self::$config[self::entity()] = new \Origami\Entity\Config(self::entity());
        }
		
		// Si c'est une requête
		if (method_exists('\Origami\Entity\Db\Query', $name)) {
			
			$query = new \Origami\Entity\Db\Query(self::$config[self::entity()]);
			
			return call_user_func_array(array($query, $name), $arguments);
		}
	}
	
	public static function origami()
    {
        $CI =& get_instance();
		
		return (isset($CI->origami) && $CI->origami instanceof \Origami) ? $CI->origami->getConfig() : array();
    }
	
	/**
     * Nom de la classe
     * @return string
     */
    public static function entity()
    {
        return get_called_class();
    }

    /**
     * Nom de la base de donnée
     * @return string
     */
    public static function database()
    {
        return explode('\\', self::getClass())[1];
    }
	
    /**
     * Nom de la table
     * @return string
     */
    public static function table()
    {
        return static::$table;
    }

    /**
     * Nom de la clé primaire
     * @return string
     */
    public static function primaryKey()
    {
        return static::$primary_key;
    }

    /**
     * Liste des champs
     * @return array
     */
    public static function fields()
    {
        return static::$fields;
    }

    /**
     * Liste des associations
     * @return array
     */
    public static function associations()
    {
        return static::$associations;
    }

    /**
     * Liste des validateurs
     * @return array
     */
    public static function validations()
    {
        return static::$validations;
    }
	
	/**
     * Liste des validateurs
     * @return array
     */
    public static function config()
    {        
        return self::$config[self::entity()];
    }

}

/* End of file Orm_entity.php */
/* Location: ./application/libraries/Orm_entity.php */
