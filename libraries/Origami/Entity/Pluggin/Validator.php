<?php

namespace Origami\Entity\Pluggin;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Validator implements PlugginInterface
{   
    /**
     * Nom du pluggin
     * @var string
     */
    private $_name = 'validator';
    
    /**
     * Object entité
     * @var \Origami\Entity\Core\Entity
     */
	private $_entity;
        
    /**
     * Liste des champs a valider
     * @var array $validations
     */
    private $validations = array();

    /**
     * Initalisateur
     */
    public function initialize() {
        // Charge les dépendances
        $config = $this->_entity->getPlugging('config');
        $storage = $this->_entity->getPlugging('storage');
        
        // Si le pluggin config existe
        if ($config === FALSE) {
            exit("Impossible de charger le plugging \Origami\Plugging\Config");
        }
        
        // Si le pluggin storage existe
        if ($storage === FALSE) {
            exit("Impossible de charger le plugging \Origami\Plugging\Storage");
        }
        

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
     * Transfère l'entité
     * @param \Origami\Entity\Manager\Origami\Entity\Core\EntityInterface $entity
     */
    public function setEntity(Origami\Entity\Core\EntityInterface $entity) {
        // Récupère l'entité
        $this->_entity =& $entity;
        
        // Initialise le pluggin
        $this->initialize();
    }
    
    /**
     * Retourne le nom du pluggin
     * @return string
     */
    public function getName() {
        return $this->_name;
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
        
        // Accès au pluggin storage
        $storage = $this->_entity->getPlugging('storage');

        // Lance la validation sur tous les champs
        foreach ($this->validations as $validation) {
            // Récupère l'instance du champs
            $field = $storage->get($validation->getField());

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

}

/* End of file Validator.php */
/* Location: ./libraries/Origami/Entity/Manager/Validator.php */
