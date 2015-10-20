<?php

namespace Origami\Entity\Pluggin;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Query implements PlugginInterface
{    
    /**
     * Nom du pluggin
     * @var string
     */
    private $_name = 'query';
    
    /**
     * Object entité
     * @var \Origami\Entity\Core\Entity
     */
	private $_entity;
    
    /**
     * Initalisateur
     */
    public function initialize() {
        // Charge la dépendance du pluggin config 
        $config = $this->_entity->getPlugging('config');
        
        if ($config === FALSE) {
            exit("Impossible de charger le plugging \Origami\Plugging\Config");
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
     * Ajoute une clé primaire
     * @param \Origami\Entity\Shema\PrimaryKey $primary_key
     */
    public function setPrimaryKey(\Origami\Entity\Shema\PrimaryKey &$primary_key, $value)
    {
        \Origami\DB::get($this->config->getDataBase())->where($primary_key->getName(), $value);

        return $this;
    }

    /**
     * Ajoute une association
     * @param \Origami\Entity\Shema\Association $association
     */
    public function setAssociation(\Origami\Entity\Shema\Association &$association)
    {    
        // Recherche le type d'association
        switch ($association->getType()) {
            case \Origami\Entity\Shema\Association::TYPE_HAS_ONE:
               \Origami\DB::get($this->config->getDataBase())->where($association->getPrimaryKey(), $association->getValue())->limit(1);
                break;
            case \Origami\Entity\Shema\Association::TYPE_HAS_MANY:
               \Origami\DB::get($this->config->getDataBase())->where($association->getForeignKey(), $association->getValue());
                break;
            case \Origami\Entity\Shema\Association::TYPE_BELONGS_TO:
               \Origami\DB::get($this->config->getDataBase())->where($association->getPrimaryKey(), $association->getValue())->limit(1);
                break;
        }

        return $this;
    }
    
}

/* End of file Query.php */
/* Location: ./libraries/Origami/Entity/Manager/Query.php */
