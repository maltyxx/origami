<?php

namespace Origami;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Entity
{
    /**
     * Instance de CodeIgniter
     * @var \stdClass 
     */
    protected $_CI;

    /**
     *
     * @var \Origami\Entity\Config
     */
    protected $_config;

    /**
     *
     * @var \Origami\Entity\Db\Database
     */
    protected $_database;

    /**
     *
     * @var \Origami\Entity\Db\Query
     */
    protected $_query;

    /**
     *
     * @var \Origami\Entity\Data\Storage
     */
    protected $_storage;

    /**
     *
     * @var \Origami\Entity\Association
     */
    protected $_association;

    /**
     *
     * @var \Origami\Entity\Validator
     */
    protected $_validator;

    /**
     * Constructeur
     * @param NULL|integer|\Origami\Entity\Schema\Association $data
     */
    function __construct($data = NULL)
    {
        $this->initialize($data);
    }

    /**
     * Si un champ est définie
     * @param type $name
     * @return boolean
     */
    public function __isset($name)
    {
        return ($this->_storage->getValue($name) !== FALSE);
    }

    /**
     * Récupère la valeur d'un champ
     * @param type $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_storage->getValue($name);
    }

    /**
     * Modifie la valeur d'un champ
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_storage->set($name, $value);
    }

    /**
     * Passage d'objets en appelant la methode du nom de la relation
     * @param string $name
     * @param array $arguments
     * @return Orm_entity
     */
    public function __call($name, $arguments)
    {
        try {
            // Si c'est une requête
            if (method_exists($this->_query, $name)) {
                return call_user_func_array(array($this->_query, $name), $arguments);

                // Si c'est une association
            } else if (($association = $this->_association->get($name)) !== FALSE) {
                // Retoune le nouveau modèle associé
                return $association->associated();

                // Sinon, il y a une erreur
            } else {
                throw new Exception("L'association $name est introuvable dans le modèle ".get_class($this).PHP_EOL);
            }
        } catch (Exception $exception) {
            exit("Origami a rencontré un problème : {$exception->getMessage()}");
        }
    }

    /**
     * Initialisation
     * @param NULL|integer|\Origami\Entity\Schema\Association $data
     */
    private function initialize($data = NULL)
    {
        // Instance de CodeIgniter
        $this->_CI = & get_instance();

        // Gestionnaire de configuration
        $this->_config = new \Origami\Entity\Config($this->_CI->origami, $this);

        // Gestionnaire de la base de donnée
        $this->_database = new \Origami\Entity\Db\Database($this->_config);

        // Gestionnaire de stockage
        $this->_storage = new \Origami\Entity\Data\Storage($this->_config);

        // Gestionnaire de Requête
        $this->_query = new \Origami\Entity\Db\Query($this->_config, $this->_database, $this->_storage);

        // Gestionnaire d'association
        $this->_association = new \Origami\Entity\Association($this->_config, $this->_storage);

        // Gestionnaire de validation
        $this->_validator = new \Origami\Entity\Validator($this->_config, $this->_storage);

        // Si la variable $data est un entier, c'est une clé primaire
        if (is_numeric($data)) {
            // Récupère l'objet grêce à la clé primaire
            $object = $this->_query->setPrimaryKey(new \Origami\Entity\Shema\Primarykey($this->_config), $data)->find_one();

            // Si l'objet est trouvé
            if ($object instanceof \Origami\Entity) {
                // Insère les donnée en silence
                $this->_storage->set($object->get(), NULL, TRUE);
            }

            // Si la variable $data est une instance de la classe Orm_association
        } else if ($data instanceof \Origami\Entity\Shema\Association) {
            $this->_query->setAssociation($data);

            // Si la variable $data est un tableau
        } else if (is_array($data) && !empty($data)) {
            $this->_storage->set($data);
        }
    }

    /**
     * Liste des valeurs du modèle
     * @param mixed $index
     * @return mixed
     */
    public function get($index = NULL)
    {
        return $this->_storage->getValue($index);
    }

    /**
     * Obtien l'objet Storage
     * @param mixed $index
     * @return \Origami\Entity\Data\Storage
     */
    public function getStorage()
    {
        return $this->_storage;
    }

    /**
     * Nom de la classe
     * @return string
     */
    public function getClass()
    {
        return get_class($this);
    }

    /**
     * Nom de la base de donnée
     * @return string
     */
    public function getDatabase()
    {
        return explode('\\', $this->getClass())[1];
    }

    /**
     * Nom de la table
     * @return string
     */
    public function getTable()
    {
        return (isset(static::$table)) ? static::$table : NULL;
    }

    /**
     * Nom de la clé primaire
     * @return string
     */
    public function getPrimaryKey()
    {
        return (isset(static::$primary_key)) ? static::$primary_key : NULL;
    }

    /**
     * Liste des champs
     * @return array
     */
    public function getFields()
    {
        return (isset(static::$fields)) ? static::$fields : array();
    }

    /**
     * Liste des associations
     * @return array
     */
    public function getAssociations()
    {
        return (isset(static::$associations)) ? static::$associations : array();
    }

    /**
     * Liste des validateurs
     * @return array
     */
    public function getValidations()
    {
        return (isset(static::$validations)) ? static::$validations : array();
    }

    /**
     * Valide un modèle et retourne ses erreurs
     * @return array|boolean
     */
    public function validate()
    {
        return $this->_validator->validate();
    }

    /**
     * Si un modèle est valide
     * @return boolean
     */
    public function is_valid()
    {
        return $this->_validator->is_valid();
    }

}

/* End of file Orm_entity.php */
/* Location: ./application/libraries/Orm_entity.php */
