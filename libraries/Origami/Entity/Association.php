<?php

namespace Origami\Entity;

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
     * @param \Origami\Entity\Config $config
     * @param \Origami\Entity\Data\Storage $storage
     */
    public function __construct(\Origami\Entity\Config $config, \Origami\Entity\Data\Storage $storage)
    {
        // Le stockage
        $this->storage =& $storage;
        
        // Configuration des associations
        $associations = $config->getAssociation();

        // Si il y a pas de champs a valider
        if (empty($associations)) {
            return FALSE;
        }
        
        // Si il y a des champs a valider
        foreach ($associations as $association) {           
            $this->associations[$association['association_key']] = new \Origami\Entity\Shema\Association($association);
        }
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

}

/* End of file Orm_association.php */
/* Location: ./application/libraries/Orm_association.php */
