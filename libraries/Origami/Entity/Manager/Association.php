<?php

namespace Origami\Entity\Manager;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Association
{
    /**
     * Stockage
     * @var \Origami\Entity\Data\Storage $storage
     */
    private $storage;
    
    /**
     * Les associations
     * @var array $associations
     */
    private $associations = array();
    
    /**
     * Constructeur
     * @param \Origami\Entity\Manager\Config $config
     * @param \Origami\Entity\Data\Storage $storage
     */
    public function __construct(\Origami\Entity\Manager\Config $config, \Origami\Entity\Manager\Storage $storage)
    {
        // Instance du gestionnaire de configuration
        $this->setConfig($config);

        // Instance du gestionnaire de stockage
        $this->setStorage($storage);
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
            // Valeur de la clé
            $value = (\Origami\Entity\Shema\Association::TYPE_HAS_MANY !== $this->associations[$index]->getType()) ?
                $this->storage->get($this->associations[$index]->getForeignKey())->getValue():
                $this->storage->get($this->associations[$index]->getPrimaryKey())->getValue();
            
            // Modifie la valeur
            $this->associations[$index]->setValue($value);
            
            // Retourne l'association
            return $this->associations[$index];

        // Si le champ n'est pas trouvé
        } else {
            return FALSE;
        }
    }
    
    /**
     * Renseigne le gestionnaire d'erreur
     * @param \Origami\Entity\Manager\Config $config
     * @return boolean
     */
    private function setConfig(\Origami\Entity\Manager\Config &$config)
    {
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
     * Renseigne le gestionnaire de stockage
     * @param \Origami\Entity\Manager\Storage $storage
     */
    private function setStorage(\Origami\Entity\Manager\Storage &$storage)
    {
        $this->storage = &$storage;
    }

}

/* End of file Association.php */
/* Location: ./libraries/Origami/Entity/Manager/Association.php */
