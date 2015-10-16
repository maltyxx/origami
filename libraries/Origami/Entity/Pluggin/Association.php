<?php

namespace Origami\Entity\Pluggin;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Association extends PlugginInterface
{
    /**
     * Nom du pluggin
     * @var string
     */
    private $_name = 'association';
    
    /**
     * Object entité
     * @var \Origami\Entity\Core\Entity
     */
	private $_entity;
        
    /**
     * Les associations
     * @var array $associations
     */
    private $associations = array();
        
    /**
     * Initalisateur
     */
    public function initialize() {
        // Charge les pluggins
        $config = $this->_entity->getPlugging('config');
        $storage = $this->_entity->getPlugging('storage');
        
        if ($config === FALSE) {
            exit("Impossible de charger le plugging \Origami\Plugging\Config");
        }
        
        if ($storage === FALSE) {
            exit("Impossible de charger le plugging \Origami\Plugging\Storage");
        }
        
        // Configuration des associations
        $associations = $config->getAssociation();

        // Si il y a pas de champ a valider
        if (empty($associations)) {
            return FALSE;
        }
        
        // Si il y a des champ a valider
        foreach ($associations as $association) {
            $this->associations[$association['association_key']] = new \Origami\Entity\Shema\Association($association);
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
     * Trouve une relation
     * @param string|NULL $index
     * @return boolean|\Origami\Entity\Shema\Association
     */
    public function get($index = NULL)
    {
        // Si l'index est NULL
        if ($index === NULL) {
            return $this->associations;

        // Si l'index existe
        } else if (isset($this->associations[$index]) && $this->associations[$index] instanceof \Origami\Entity\Shema\Association) {
            // Pluggin storage
            $storage = $this->_entity->getPlugging('storage');
            
            // Valeur de la clé
            $value = (\Origami\Entity\Shema\Association::TYPE_HAS_MANY !== $this->associations[$index]->getType()) ?
                $storage->get($this->associations[$index]->getForeignKey())->getValue():
                $storage->get($this->associations[$index]->getPrimaryKey())->getValue();
            
            // Modifie la valeur
            $this->associations[$index]->setValue($value);
            
            // Retourne l'association
            return $this->associations[$index];

        // Si le champ n'est pas trouvé
        } else {
            return FALSE;
        }
    }
        
}

/* End of file Association.php */
/* Location: ./libraries/Origami/Entity/Manager/Association.php */
