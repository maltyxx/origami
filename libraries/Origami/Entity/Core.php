<?php

namespace Origami\Entity;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Core
{
    /**
     * Table
     * @var string $table
     */
	protected static $table;
	
    /**
     * Clé primaire
     * @var string $primary_key
     */
	protected static $primary_key;
	
    /**
     * Champs
     * @var array $fields
     */
	protected static $fields = array();
	
    /**
     * Associations
     * @var array $associations
     */
	protected static $associations = array();
	
    /**
     * Validations
     * @var array $validations
     */
	protected static $validations = array();
	
    /**
     * Configuration
     * @var \Origami\Entity\Manager\Config $config
     */
	protected static $config;
	
	public static function __callStatic($name, $arguments = array()) {
		        
        self::$config = new \Origami\Entity\Manager\Config(self::entity());
        
		// Si c'est une requête
		if (method_exists('\Origami\Entity\Db\Query', $name)) {
			
			$query = new \Origami\Entity\Db\Query(self::$config);
			
			return call_user_func_array(array($query, $name), $arguments);
		}
	}
	
    /**
     * Configuration général
     * @return array
     */
	public static function origami()
    {
        $CI =& get_instance();
		
		return (isset($CI->origami) && $CI->origami instanceof \Origami) ? $CI->origami->getConfig() : array();
    }
	
	/**
     * La classe
     * @return string
     */
    public static function entity()
    {
        return get_called_class();
    }

    /**
     * La base de donnée
     * @return string
     */
    public static function database()
    {
        return explode('\\', self::entity())[1];
    }
	
    /**
     * La table
     * @return string
     */
    public static function table()
    {
        return static::$table;
    }

    /**
     * La clé primaire
     * @return string
     */
    public static function primaryKey()
    {
        return static::$primary_key;
    }

    /**
     * Les champs
     * @return array
     */
    public static function fields()
    {
        return static::$fields;
    }

    /**
     * Les associations
     * @return array
     */
    public static function associations()
    {
        return static::$associations;
    }

    /**
     * Les validateurs
     * @return array
     */
    public static function validations()
    {
        return static::$validations;
    }
	
	/**
     * La configuration de l'entité
     * @return array
     */
    public static function config()
    {        
        return self::$config;
    }

}

/* End of file Core.php */
/* Location: ./libraries/Origami/Entity/Core.php */
