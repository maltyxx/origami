<?php

namespace Origami\Entity\Data;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Storage
{
    /**
     * Liste des champs
     * @var array $fields
     */
    private $fields = array();
    
    /**
     * Liste des champs à mettre à jour
     * @var array $update
     */
    private $update = array();
    
    /**
     * Constructeur
     * @param \Origami\Entity\Config $config
     */
    public function __construct(\Origami\Entity\Config &$config)
    {
        $this->initialize($config);
    }
    
    /**
     * Initialisateur
     * @param \Origami\Entity\Config $config
     */
    public function initialize(\Origami\Entity\Config &$config)
    {
        foreach ($config->getField() as $field) {
            $this->fields[$field['name']] = new \Origami\Entity\Shema\Field($field, NULL, TRUE);
        }
    }
    
    /**
     * Recherche un ou plusieurs champs
     * @param string|NULL $index
     * @return array|\Origami\Entity\Shema\Field|boolean
     */
    public function get($index = NULL)
    {
        if ($index === NULL) {
            return $this->fields;
        } else if (isset($this->fields[$index])) {
            return $this->fields[$index];
        } else {
            return FALSE;
        }
    }
    
    /**
     * Recherche la valeur d'un ou plusieurs champs
     * @param type $index
     * @return mixed
     */
    public function getValue($index = NULL)
    {
        if ($index === NULL) {
            $fields = array();

            foreach ($this->fields as $field) {
                $fields[$field->getName()] = $field->getValue();
            }

            return $fields;
        } else if (isset($this->fields[$index])) {
            return $this->fields[$index]->getValue();
        } else {
            return FALSE;
        }
    }
    
    /**
     * Recherche les champs qui ont été mis à jour
     * @param type $index
     * @return array|\Origami\Entity\Shema\Field|boolean
     */
    public function getUpdate($index = NULL)
    {
        if ($index === NULL) {
            return $this->update;
        } else if (isset($this->update[$index])) {
            return $this->update[$index];
        } else {
            return FALSE;
        }
    }
    
    /**
     * Modifie la valeur d'un ou de plusieurs champss
     * @param string|NULL $index
     * @param mixed $value
     * @param boolean $silence
     */
    public function set($index, $value = NULL, $silence = FALSE)
    {
        // Si l'index est un tableau
        if (is_array($index)) {
            foreach ($index as $key => $value) {
                $this->set($key, $value, $silence);
            }
            // Si l'index n'est pas un tableau
        } else if (isset($this->fields[$index])) {
            $this->fields[$index]->setValue($value, $silence);

            // Si le mode silence est désactivé et si la valeur a changé
            if ($silence === FALSE && $this->fields[$index]->isUpdate()) {
                $this->update[$index] = $this->fields[$index];
            }
        }
    }
    
    /**
     * Si il y a des champs à mettre à jour
     * @return boolean
     */
    public function isUpdate()
    {
        return (!empty($this->update));
    }
    
    /**
     * Efface les champs à mettre à jour
     */
    public function cleanUpdate()
    {
        $this->update = array();
    }

}

/* End of file Storage.php */
/* Location: ./libraries/Origami/Data/Storage.php */
