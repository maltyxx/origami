<?php

namespace Origami\Entity;

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
    public function __construct(\Origami &$origami, \Origami\Entity &$entity)
    {
        // Configuration générale
        $this->setOrigami($origami);

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
     * Configuration générale
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

    public function setOrigami(\Origami &$origami)
    {
        $this->origami = $origami->getConfig();
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setClass(\Origami\Entity &$entity)
    {
        $this->class = $entity->getClass();
    }

    public function getDataBase()
    {
        return $this->database;
    }

    public function setDataBase(\Origami\Entity &$entity)
    {
        $this->database = $entity->getDataBase();
    }

    public function getTable()
    {
        return $this->table;
    }

    public function setTable(\Origami\Entity &$entity)
    {
        $this->table = $entity->getTable();
    }

    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    public function setPrimaryKey(\Origami\Entity &$entity)
    {
        $this->primary_key = $entity->getPrimaryKey();
    }

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

    public function setField(\Origami\Entity &$entity)
    {
        $this->fields = array();

        foreach ($entity->getFields() as $field) {
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

    public function setAssociation(\Origami\Entity &$entity)
    {
        $this->associations = array();

        foreach ($entity->getAssociations() as $field) {
            $this->associations[$field['association_key']] = $field;
        }
    }

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

    public function setValidation(\Origami\Entity &$entity)
    {
        $this->validations = array();

        foreach ($entity->getValidations() as $field) {
            $this->validations[$field['field']] = $field;
        }
    }

}

/* End of file Config.php */
/* Location: ./libraries/Origami/Entity/Config.php */
