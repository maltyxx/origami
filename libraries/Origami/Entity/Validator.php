<?php

namespace Origami\Entity;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Validator
{
    private $config;
    
    private $storage;
    
    private $validations = array();

    public function __construct(\Origami\Entity\Config $config, \Origami\Entity\Data\Storage $storage)
    {
        // Configuration
        $this->config = & $config;

        // Stockage
        $this->storage = & $storage;

        // Champs a valider
        $this->set($config->getValidation());
    }

    private function set(array $validations)
    {
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
     * Valide un modèle et retourne ses erreurs
     * @return array|boolean
     */
    public function validate()
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
     * Si un modèle est valide
     * @return boolean
     */
    public function is_valid()
    {
        return ($errors = $this->validate() && empty($errors));
    }

}

/* End of file Validator.php */
/* Location: ./libraries/Validator.php */
