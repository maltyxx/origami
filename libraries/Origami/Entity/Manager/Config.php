<?php

namespace Origami\Entity\Manager;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Config
{
    /**
     * Configuration Générale
     * @var array 
     */
    private $origami = array();

    /**
     * Nom de la classe
     * @var string 
     */
    private $class;

    /**
     * Nom de la base de donnée
     * @var string 
     */
    private $database;

    /**
     * Nom de la table
     * @var string 
     */
    private $table;

    /**
     * Nom de la clé primaire
     * @var string 
     */
    private $primary_key;

    /**
     * Liste des champs
     * @var array
     */
    private $fields = array();

    /**
     * Liste des champs cryptés
     * @var array 
     */
    private $fields_encrypt = array();

    /**
     * Liste des champs binaires
     * @var array 
     */
    private $fields_binary = array();

    /**
     * Configuration des associations
     * @var array
     */
    private $associations = array();

    /**
     * Configuration des validations
     * @var array 
     */
    private $validations = array();

    /**
     * Contructeur
     * @param array $config
     * @param \Origami\Entity $entity
     */
    public function __construct($entity)
    {
        // Configuration générale
        $this->setOrigami($entity);

        // Configuration de la classe
        $this->setClass($entity);

        // Configuration de la base de donnée
        $this->setDatabase($entity);

        // Configuration de la table
        $this->setTable($entity);

        // Configuration de la la clé primaire
        $this->setPrimaryKey($entity);

        // Configuration des champs
        $this->setField($entity);

        // Configuration des associations
        $this->setAssociation($entity);

        // Configuration des validateurs
        $this->setValidation($entity);
    }

    /**
     * Retourne la configuration générale
     * @param type $index
     * @return mixed
     */
    public function getOrigami($index = NULL)
    {
        // Si l'index est NULL
        if ($index === NULL) {
            return $this->origami;

            // Si l'index existe
        } else if (isset($this->origami[$index])) {
            return $this->origami[$index];
            // Si le champ n'est pas trouvé
        } else {
            return FALSE;
        }
    }
    
    /**
     * Renseigne la configuration générale
     * @param string $entity
     */
    public function setOrigami($entity)
    {
        $this->origami = $entity::origami();
    }
    
    /**
     * Retourne le nom de l'entité
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Renseigne l'entité
     * @param string $entity
     */
    public function setClass($entity)
    {
        $this->class = $entity::entity();
    }

    /**
     * Retourne le nom de la base de donnée
     * @return string
     */
    public function getDataBase()
    {
        return $this->database;
    }

    /**
     * Renseigne la base de donnée
     * @param string $entity
     */
    public function setDataBase($entity)
    {
        $this->database = $entity::dataBase();
    }

    /**
     * Retourne le nom de la table
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Renseigne la table
     * @param string $entity
     */
    public function setTable($entity)
    {
        $this->table = $entity::table();
    }

    /**
     * Retourne le nom de la clé primaire
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    /**
     * Renseigne la clé primaire
     * @param string $entity
     */
    public function setPrimaryKey($entity)
    {
        $this->primary_key = $entity::primaryKey();
    }

    /**
     * Retourne la liste des champs
     * @return mixed
     */
    public function getField($index = NULL)
    {
        // Si l'index est NULL
        if ($index === NULL) {
            return $this->fields;

            // Si l'index existe
        } else if (isset($this->fields[$index])) {
            return $this->fields[$index];

            // Si le champ n'est pas trouvé
        } else {
            return FALSE;
        }
    }

    /**
     * Renseigne les champs
     * @param string $entity
     */
    public function setField($entity)
    {
        $this->fields = array();

        foreach ($entity::fields() as $field) {
            $this->fields[$field['name']] = $field;

            // Si le champ est crypté
            if (isset($field['encrypt']) && $field['encrypt'] === TRUE) {
                $this->fields_encrypt[$field['name']] = $field;

            // Si le champ est binaire
            } else if (isset($field['binary']) && $field['binary'] === TRUE) {
                $this->fields_binary[$field['name']] = $field;
            }
        }
    }

    /**
     * Retourne la liste des champs encryptés
     * @return mixed
     */
    public function getFieldEncrypt($index = NULL)
    {
        // Si l'index est NULL
        if ($index === NULL) {
            return $this->fields_encrypt;

            // Si l'index existe
        } else if (isset($this->fields_encrypt[$index])) {
            return $this->fields_encrypt[$index];

            // Si le champ n'est pas trouvé
        } else {
            return FALSE;
        }
    }

    /**
     * Retourne la liste des champs binaires
     * @return mixed
     */
    public function getFieldBinary($index = NULL)
    {
        // Si l'index est NULL
        if ($index === NULL) {
            return $this->fields_binary;

            // Si l'index existe
        } else if (isset($this->fields_binary[$index])) {
            return $this->fields_binary[$index];

            // Si le champ n'est pas trouvé
        } else {
            return FALSE;
        }
    }

    /**
     * Retourne la liste des associations
     * @return mixed
     */
    public function getAssociation($index = NULL)
    {
        // Si l'index est NULL
        if ($index === NULL) {
            return $this->associations;

            // Si l'index existe
        } else if (isset($this->associations[$index])) {
            return $this->associations[$index];

            // Si le champ n'est pas trouvé
        } else {
            return FALSE;
        }
    }

    /**
     * Renseigne les associations
     * @param string $entity
     */
    public function setAssociation($entity)
    {
        $this->associations = array();

        foreach ($entity::associations() as $field) {
            $this->associations[$field['association_key']] = $field;
        }
    }

    /**
     * Retourne la liste des validations
     * @return mixed
     */
    public function getValidation($index = NULL)
    {
        // Si l'index est NULL
        if ($index === NULL) {
            return $this->validations;

            // Si l'index existe
        } else if (isset($this->validations[$index])) {
            return $this->validations[$index];

            // Si le champ n'est pas trouvé
        } else {
            return FALSE;
        }
    }

    /**
     * Renseigne les validations
     * @param string $entity
     */
    public function setValidation($entity)
    {
        $this->validations = array();

        foreach ($entity::validations() as $field) {
            $this->validations[$field['field']] = $field;
        }
    }

}

/* End of file Config.php */
/* Location: ./libraries/Origami/Entity/Manager/Config.php */
