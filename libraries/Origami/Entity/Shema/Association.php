<?php

namespace Origami\Entity\Shema;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Association
{
    const TYPE_HAS_ONE = 'has_one';
    const TYPE_HAS_MANY = 'has_many';
    const TYPE_BELONGS_TO = 'belongs_to';

    /**
     * Nom de l'association
     * @var string 
     */
    private $association_key;

    /**
     * Type d'association
     * @var string 
     */
    private $type;

    /**
     * Nom du modèle
     * @var string 
     */
    private $entity;

    /**
     * Nom de la clé primaire
     * @var string 
     */
    private $primary_key;

    /**
     * Nom de la clé étrangère
     * @var string 
     */
    private $foreign_key;

    /**
     * Valeur de la clé
     * @var integer 
     */
    private $value;

    /**
     * Constructeur
     * @param \Origami\Entity\Manager\Config $config
     * @param integer $value
     */
    public function __construct(array $config)
    {
        // Si la clé primaire n'est pas définie
        if (empty($config['primary_key'])) {
            $config['primary_key'] = 'id';
        }

        // Si la clé étrangère n'est pas définie
        if (empty($config['foreign_key'])) {
            $config['foreign_key'] = $config['association_key'].'_id';
        }

        $this->setAssociationKey($config['association_key']);

        $this->setType($config['type']);

        $this->setEntity($config['entity']);

        $this->setPrimaryKey($config['primary_key']);

        $this->setForeignKey($config['foreign_key']);
    }

    /**
     * Retourne le nom de l'association
     * @return string
     */
    public function getAssociationKey()
    {
        return $this->association_key;
    }

    /**
     * Modifie nom de l'association
     * @return string
     */
    public function setAssociationKey($association_key)
    {
        $this->association_key = $association_key;
    }

    /**
     * Retourne le type d'association
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Modifie le type de l'association
     * @return string
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Retourne le nom de l'entité
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Modifie le nom de l'entité
     * @return string
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
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
     * Modifie le nom de la clé primaire
     * @return string
     */
    public function setPrimaryKey($primary_key)
    {
        $this->primary_key = $primary_key;
    }

    /**
     * Retourne le nom de la clé étrangère
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreign_key;
    }

    /**
     * Modifie le nom de la clé étrangère
     * @return string
     */
    public function setForeignKey($foreign_key)
    {
        $this->foreign_key = $foreign_key;
    }

    /**
     * Retourne la valeur
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Modifie la valeur
     * @return string
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Rertourne une association
     * @return \Origami\Entity\Shema\entity
     */
    public function associated()
    {
        return new $this->entity($this);
    }

}

/* End of file Association.php */
/* Location: ./libraries/Origami/Entity/Shema/Association.php */
