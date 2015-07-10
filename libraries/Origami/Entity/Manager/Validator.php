<?php

namespace Origami\Entity\Manager;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Validator
{   
    /**
     * Instance du gestionnaire de stockage
     * @var \Origami\Entity\Manager\Storage 
     */
    private $storage;
    
    /**
     * Liste des champs a valider
     * @var array $validations
     */
    private $validations = array();
    
    /**
     * Constructeur
     * @param \Origami\Entity\Manager\Config $config
     * @param \Origami\Entity\Manager\Storage $storage
     */
    public function __construct(\Origami\Entity\Manager\Config &$config, \Origami\Entity\Manager\Storage &$storage)
    {
        // Instance du gestionnaire de configuration
        $this->setConfig($config);

        // Instance du gestionnaire de stockage
        $this->setStorage($storage);
    }

    /**
     * Valide une entité et retourne ses erreurs
     * @return array|boolean
     */
    public function validate($index = NULL)
    {
        $errors = array();

        // Si il y a pas de champs a valider
        if (empty($this->validations)) {
            return $errors;
        }

        // Lance la validation sur tous les champs
        foreach ($this->validations as $validation) {
            // Récupère l'instance du champs
            $field = $this->storage->get($validation->getField());

            // Si le champ n'est pas valide
            if (!$validation->validate($field)) {
                $errors[$validation->getField()] = sprintf($validation->getMessage(), $validation->getField(), $field->getValue());
            }
        }

        // Retourne les erreurs
        return $errors;
    }

    /**
     * Valide une entité
     * @return boolean
     */
    public function is_valid()
    {
        return ($errors = $this->validate() && empty($errors));
    }

    /**
     * Renseigne le gestionnaire d'erreur
     * @param \Origami\Entity\Manager\Config $config
     * @return boolean
     */
    private function setConfig(\Origami\Entity\Manager\Config &$config)
    {
        // Tableau des champs a valider
        $validations = $config->getValidation();

        // Si il y a pas de champs a valider
        if (empty($validations)) {
            return FALSE;
        }

        // Si il y a des champs a valider
        foreach ($validations as $validation) {
            $this->validations[$validation['field']] = new \Origami\Entity\Shema\Validation($validation);
        }
    }

    /**
     * Renseigne le gestionnaire de stockage
     * @param \Origami\Entity\Manager\Storage $storage
     */
    private function setStorage(\Origami\Entity\Manager\Storage &$storage)
    {
        $this->storage = &$storage;
    }

}

/* End of file Validator.php */
/* Location: ./libraries/Origami/Entity/Manager/Validator.php */
