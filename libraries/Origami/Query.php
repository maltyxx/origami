<?php

namespace Origami\Entity\Db;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Query
{
	private $config;

    public function __construct(\Origami\Entity\Config $config) {
        $this->config =& $config;
	}
	
	/**
     * Ajoute une clé primaire
     * @param \Origami\Entity\Shema\PrimaryKey $primary_key
     */
    public function setPrimaryKey(\Origami\Entity\Shema\PrimaryKey &$primary_key, $value)
    {        
        \Origami\Database::link($this->config->getDataBase())->where($primary_key->getName(), $value);

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
               \Origami\Database::link($this->config->getDataBase())->where($association->getPrimaryKey(), $association->getValue())->limit(1);
                break;
            case \Origami\Entity\Shema\Association::TYPE_HAS_MANY:
               \Origami\Database::link($this->config->getDataBase())->where($association->getForeignKey(), $association->getValue());
                break;
            case \Origami\Entity\Shema\Association::TYPE_BELONGS_TO:
               \Origami\Database::link($this->config->getDataBase())->where($association->getPrimaryKey(), $association->getValue())->limit(1);
                break;
        }

        return $this;
    }
	
    /**
     * Génère un WHERE en SQL
     * @param mixe $key
     * @param NULL|string|int|float $value
     * @param boolean $escape
     */
    public function where($key, $value = NULL, $escape = TRUE)
    {
       \Origami\Database::link($this->config->getDataBase())->where($key, $value, $escape);
        
        return $this;
    }

    /**
     * Génère un WHERE % OR en SQL
     * @param mixe $key
     * @param NULL|string|int|float $value
     * @param boolean $escape
     */
    public function or_where($key, $value = NULL, $escape = TRUE)
    {
       \Origami\Database::link($this->config->getDataBase())->or_where($key, $value, $escape);

        return $this;
    }

    /**
     * Génère un WHERE IN en SQL
     * @param mixe $key
     * @param NULL|string|int|float $value
     * @param boolean $escape
     */
    public function where_in($key = NULL, $values = NULL)
    {
       \Origami\Database::link($this->config->getDataBase())->where_in($key, $values);

        return $this;
    }

    /**
     * Génère un WHERE NOT IN en SQL
     * @param mixe $key
     * @param NULL|string|int|float $value
     * @param boolean $escape
     */
    public function where_not_in($key = NULL, $values = NULL)
    {
       \Origami\Database::link($this->config->getDataBase())->where_not_in($key, $values);

        return $this;
    }

    /**
     * Génère un WHERE OR % NOT IN en SQL
     * @param mixe $key
     * @param NULL|string|int|float $value
     * @param boolean $escape
     */
    public function or_where_not_in($key = NULL, $values = NULL)
    {
       \Origami\Database::link($this->config->getDataBase())->or_where_not_in($key, $values);

        return $this;
    }

    /**
     * Génère un LIKE en SQL
     * @param mixe $field
     * @param string $match
     * @param string $side
     */
    public function like($field, $match = '', $side = 'both')
    {
       \Origami\Database::link($this->config->getDataBase())->like($field, $match, $side);

        return $this;
    }

    /**
     * Génère un OR % LIKE en SQL
     * @param mixe $field
     * @param string $match
     * @param string $side
     */
    public function or_like($field, $match = '', $side = 'both')
    {
       \Origami\Database::link($this->config->getDataBase())->or_like($field, $match, $side);

        return $this;
    }

    /**
     * Génère un NOT LIKE en SQL
     * @param mixe $field
     * @param string $match
     * @param string $side
     */
    public function not_like($field, $match = '', $side = 'both')
    {
       \Origami\Database::link($this->config->getDataBase())->or_like($field, $match, $side);

        return $this;
    }

    /**
     * Génère un OR % NOT LIKE en SQL
     * @param mixe $field
     * @param string $match
     * @param string $side
     */
    public function or_not_like($field, $match = '', $side = 'both')
    {
       \Origami\Database::link($this->config->getDataBase())->or_not_like($field, $match, $side);

        return $this;
    }

    /**
     * Génère un GROUP BY en SQL
     * @param string $by
     */
    public function group_by($by)
    {
       \Origami\Database::link($this->config->getDataBase())->group_by($by);

        return $this;
    }

    /**
     * Génère un HAVING en SQL
     * @param string $key
     * @param string $value
     * @param boolean $escape
     */
    public function having($key, $value = '', $escape = TRUE)
    {
       \Origami\Database::link($this->config->getDataBase())->having($key, $value, $escape);

        return $this;
    }

    /**
     * Génère un OR HAVING en SQL
     * @param string $key
     * @param string $value
     * @param boolean $escape
     */
    public function or_having($key, $value = '', $escape = TRUE)
    {
       \Origami\Database::link($this->config->getDataBase())->or_having($key, $value, $escape);

        return $this;
    }

    /**
     * Génère un ORDER BY en SQL
     * @param string $orderby
     * @param string $direction
     */
    public function order_by($orderby, $direction = '')
    {
       \Origami\Database::link($this->config->getDataBase())->order_by($orderby, $direction);

        return $this;
    }

    /**
     * Génère un LIMIT en SQL
     * @param string $value
     * @param string $offset
     */
    public function limit($value, $offset = '')
    {
       \Origami\Database::link($this->config->getDataBase())->limit($value, $offset);

        return $this;
    }

    /**
     * Génère un OFFSET en SQL
     * @param string $offset
     */
    public function offset($offset)
    {
       \Origami\Database::link($this->config->getDataBase())->offset($offset);

        return $this;
    }
	
	/**
     * Requête de lecture
     */
    private function select()
    {
        // Par défault utilise un select *
        \Origami\Database::link($this->config->getDataBase())->select('*');
        
        // Si le cryptage est activé et si il y a des champs cryptés
        if ($this->config->getOrigami('encryption_enable')) {
            // Les champs cryptés
            $fields = $this->config->getFieldEncrypt();
            
            // Si il y a des champs cryptés
            if (!empty($fields)) {
                foreach ($fields as $field) {
                   \Origami\Database::link($this->config->getDataBase())->select("CONVERT(AES_DECRYPT(FROM_BASE64(`{$field['name']}`), UNHEX('{$this->config->getConfig()->getOrigami('encryption_key')}'), UNHEX(`vector`)) USING 'utf8') AS `{$field['name']}`", FALSE);
                }
            }
        }

        // Si le binaire est activé et si il y a des champs binaires
        if ($this->config->getOrigami('binary_enable')) {
            // Les champs binaires
            $fields = $this->config->getFieldBinary();
            
            // Si il y a des champs binaires
            if (!empty($fields)) {
                foreach ($fields as $field) {
                   \Origami\Database::link($this->config->getDataBase())->select("TO_BASE64(`{$field['name']}`) AS `{$field['name']}`", FALSE);
                }
            }
        }

        return\Origami\Database::link($this->config->getDataBase());
    }
	
	/**
     * Requête de résultat
     * @return array
     */
    private function result()
    {       
        // Requête
        $results = $this->select()->from($this->config->getTable())->get()->result_array();

        // Si il y a pas de résultat
        if (empty($results)) {
            return array();

            // Si il y a des résultats
        } else {
            $objects = array();

            foreach ($results as $result) {
                $class = $this->config->getClass();
                $object = new $class();

                foreach ($result as $key => $value) {
                    $object->getStorage()->set($key, $value, TRUE);
                }

                $objects[] = $object;
            }

            return $objects;
        }
    }

    /**
     * Trouve un ou plusieurs modèles
     * @return array
     */
    public function find()
    {
		$objects = $this->result();
		
		// Si aucun résultat trouvé
        if (empty($objects)) {
            return array();
        }

        // Retoune les objets
        return $objects;
    }

    /**
     * Trouve un modèle
     * @return null|\Origami\Entity
     */
    public function find_one()
    {        
		// Limite la requête a un objet
        \Origami\Database::link($this->config->getDataBase())->limit(1);

        // Exécute la requête
        $objects = $this->find();

        // Retoune le premier résultat
        return (isset($objects[0])) ? $objects[0] : NULL;
    }
	
	/**
     * Count les résultats trouvé
     * @return integer
     */
    public function count()
    {
		 return (int) \Origami\Database::link($this->config->getDataBase())->count_all_results($this->config->getTable());
    }
		
    /**
     * Efface un ou plusieurs modèle
     * @return boolean
     */
    public function delete()
    {
        return \Origami\Database::link($this->config->getDataBase())->delete($this->config->getTable());
    }
}

/* End of file Orm_entity.php */
/* Location: ./application/libraries/Orm_entity.php */
