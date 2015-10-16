<?php

namespace Origami\Entity\Core;

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
	public static $table;
	
    /**
     * Clé primaire
     * @var string $primary_key
     */
	public static $primary_key;
	
    /**
     * Champs
     * @var array $fields
     */
	public static $fields = array();
	
    /**
     * Associations
     * @var array $associations
     */
	public static $associations = array();
	
    /**
     * Validations
     * @var array $validations
     */
	public static $validations = array();
		
	/**
	 * Factory
	 * @param string $name
	 * @param array $arguments
	 * @return \Origami\Entity\Manager\Query
	 */
	public static function __callStatic($name, $arguments = array()) {

        $config = new \Origami\Entity\Manager\Config(self::entity());

		// Si c'est une requête
		if (method_exists('\Origami\Entity\Manager\Query', $name)) {

			$query = new \Origami\Entity\Manager\Query($config);

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
        return explode('\\', self::entity())[1];
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
     * liste des validateurs
     * @return array
     */
    public static function validations()
    {
        return static::$validations;
    }
}

/* End of file Entity.php */
/* Location: ./libraries/Origami/Entity/Entity.php */
