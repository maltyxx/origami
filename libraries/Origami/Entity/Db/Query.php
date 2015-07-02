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
    /**
     *
     * @var \Origami\Entity\Config
     */
    private $config;

    /**
     *
     * @var \Origami\Entity\Db\Database
     */
    private $database;

    /**
     *
     * @var \Origami\Entity\Data\Storage
     */
    private $storage;

    /**
     * Constructeur
     * @param \Origami\Entity\Config $config
     * @param \Origami\Entity\Db\Database $database
     * @param \Origami\Entity\Data\Storage $storage
     */
    public function __construct(\Origami\Entity\Config &$config, \Origami\Entity\Db\Database &$database, \Origami\Entity\Data\Storage &$storage)
    {
        $this->initialize($config, $database, $storage);
    }
    
    private function initialize(\Origami\Entity\Config $config, \Origami\Entity\Db\Database $database, \Origami\Entity\Data\Storage $storage) {
        // Configuration
        $this->config = & $config;

        // Database
        $this->database = & $database;

        // Stockage
        $this->storage = & $storage;
    }

    /**
     * Ajoute une clé primaire
     * @param \Origami\Entity\Shema\PrimaryKey $primary_key
     */
    public function setPrimaryKey(\Origami\Entity\Shema\PrimaryKey &$primary_key, $value)
    {
        $this->database->db()->where($primary_key->getName(), $value);

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
                $this->database->db()->where($association->getPrimaryKey(), $association->getValue())->limit(1);
                break;
            case \Origami\Entity\Shema\Association::TYPE_HAS_MANY:
                $this->database->db()->where($association->getForeignKey(), $association->getValue());
                break;
            case \Origami\Entity\Shema\Association::TYPE_BELONGS_TO:
                $this->database->db()->where($association->getPrimaryKey(), $association->getValue())->limit(1);
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
        $this->database->db()->where($key, $value, $escape);

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
        $this->database->db()->or_where($key, $value, $escape);

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
        $this->database->db()->where_in($key, $values);

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
        $this->database->db()->where_not_in($key, $values);

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
        $this->database->db()->or_where_not_in($key, $values);

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
        $this->database->db()->like($field, $match, $side);

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
        $this->database->db()->or_like($field, $match, $side);

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
        $this->database->db()->or_like($field, $match, $side);

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
        $this->database->db()->or_not_like($field, $match, $side);

        return $this;
    }

    /**
     * Génère un GROUP BY en SQL
     * @param string $by
     */
    public function group_by($by)
    {
        $this->database->db()->group_by($by);

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
        $this->database->db()->having($key, $value, $escape);

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
        $this->database->db()->or_having($key, $value, $escape);

        return $this;
    }

    /**
     * Génère un ORDER BY en SQL
     * @param string $orderby
     * @param string $direction
     */
    public function order_by($orderby, $direction = '')
    {
        $this->database->db()->order_by($orderby, $direction);

        return $this;
    }

    /**
     * Génère un LIMIT en SQL
     * @param string $value
     * @param string $offset
     */
    public function limit($value, $offset = '')
    {
        $this->database->db()->limit($value, $offset);

        return $this;
    }

    /**
     * Génère un OFFSET en SQL
     * @param string $offset
     */
    public function offset($offset)
    {
        $this->database->db()->offset($offset);

        return $this;
    }

    /**
     * Requête de résultat
     * @return array
     */
    private function result()
    {
        // Requête
        $results = $this
            ->read()
            ->from($this->config->getTable())
            ->get()
            ->result_array();

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
     * Count les résultats trouvé
     * @return integer
     */
    public function count()
    {
        return (int) $this->database->db()
            ->count_all_results($this->config->getTable());
    }

    /**
     * Trouve un ou plusieurs modèles
     * @return array
     */
    public function find()
    {
        // Répuère les objets
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
        $this->database->db()->limit(1);

        // Exécute la requête
        $objects = $this->find();

        // Retoune le premier résultat
        return (isset($objects[0])) ? $objects[0] : NULL;
    }

    /**
     * Requête de lecture
     */
    private function read()
    {
        // Par défault utilise un select *
        $this->database->db()->select('*');
        
        
        
        // Si le cryptage est activé et si il y a des champs cryptés
        if ($this->config->getOrigami('encryption_enable')) {
            // Les champs cryptés
            $fields = $this->config->getFieldEncrypt();
            
            // Si il y a des champs cryptés
            if (!empty($fields)) {
                foreach ($fields as $config) {
                    $field = $this->storage->get($config['name']);
                    $this->database->db()->select("CONVERT(AES_DECRYPT(FROM_BASE64(`{$field->getName()}`), UNHEX('{$this->config->getOrigami('encryption_key')}'), UNHEX(`vector`)) USING 'utf8') AS `{$field->getName()}`", FALSE);
                }
            }
        }

        // Si le binaire est activé et si il y a des champs binaires
        if ($this->config->getOrigami('binary_enable')) {
            // Les champs binaires
            $fields = $this->config->getFieldBinary();
            
            // Si il y a des champs binaires
            if (!empty($fields)) {
                foreach ($fields as $config) {
                    $field = $this->storage->get($config['name']);
                    $this->database->db()->select("TO_BASE64(`{$field->getName()}`) AS `{$field->getName()}`", FALSE);
                }
            }
        }

        return $this->database->db();
    }

    /**
     * Requête d'écruture
     */
    private function write()
    {
        // Les champs à mettre à jour
        $fields = $this->storage->getUpdate();

        // Si il y a des champs a updater        
        if (!empty($fields)) {
            // Champs a mettre à jour
            foreach ($fields as $field) {
                // Si le cryptage est activé et qu'il y a des champs crypté
                if ($this->config->getOrigami('encryption_enable') && $field->getEncrypt()) {
                    // Récupération du champ vecteur
                    $vector = $this->storage->get('vector');
                    $value = $vector->getValue();
                    
                    // Si le vecteur n'a pas de valeur
                    if (empty($value)) {
                        // Créer un vecteur
                        $this->storage->set('vector', random_string('unique'));
                                                
                        // Recharge l'object le vecteur
                        $vector = $this->storage->get('vector');
                        
                        // Prépare l'insertion vecteur
                        $this->database->db()->set($vector->getName(), $vector->getValue(), TRUE);
                    }

                    // Encryptage de la valeur
                    $this->database->db()->set("`{$field->getName()}`", "TO_BASE64(AES_ENCRYPT('{$this->database->db()->escape_str($field->getValue())}', UNHEX('{$this->config->getOrigami('encryption_key')}'), UNHEX('{$vector->getValue()}')))", FALSE);

                    // Si le champ est un binaire 
                } else if ($this->config->getOrigami('binary_enable') && $field->getBinary()) {

                    // Transformation de la valeur
                    $this->database->db()->set("`{$field->getName()}`", "FROM_BASE64('{$this->database->db()->escape_str($field->getValue())}')", FALSE);

                    // Si c'est un champ normal
                } else {
                    $this->database->db()->set($field->getName(), $field->getValue(), TRUE);
                }
            }
        }

        return $this->database->db();
    }

    /**
     * Sauvegarde un modèle
     * @param boolean $replace
     * @param boolean $force_insert
     * @return boolean
     */
    public function save($replace = FALSE, $force_insert = FALSE)
    {
        // Clé primaire
        $field = $this->storage->get($this->config->getPrimaryKey());
        $field_value = $field->getValue();

        // Si la la requête doit être de type INSERT
        $has_insert = (empty($field_value) || $force_insert === TRUE);

        // Si il y a pas de changement
        if ($this->storage->isUpdate() === FALSE) {
            return FALSE;
        }

        // Etat de la requête
        $query = FALSE;

        // Si la requete est de type replace
        if ($replace) {
            // Exécute la requête
            $query = $this
                ->write()
                ->from($this->config->getTable())
                ->replace();

            // Si l'insertion est correcte
            if ($query === TRUE) {
                // Met a jour la clé primaire en silence
                $this->storage->set($field->getName(), $this->database->db()->insert_id(), TRUE);
            }

            // Si la requete est de type insert
        } else if ($has_insert) {
            // Exécute la requête
            $query = $this
                ->write()
                ->from($this->config->getTable())
                ->insert();

            // Si l'insertion est correcte
            if ($query === TRUE) {
                // Met a jour la clé primaire en silence
                $this->storage->set($field->getName(), $this->database->db()->insert_id(), TRUE);
            }

            // Si la requete est de type update
        } else {
            // Exécute la requête
            $query = $this
                ->write()
                ->from($this->config->getTable())
                ->where($field->getName(), $field->getValue())
                ->update();
        }

        // Vide les changements
        $this->storage->cleanUpdate();

        // La requête a échoué
        return $query;
    }

    /**
     * Efface un modèle l'instance en cours
     * @return boolean
     */
    public function remove()
    {
        $field = $this->storage->get($this->config->getPrimaryKey());
        $value = $field->getValue();

        // Si la valeur est vide
        if (empty($value)) {
            return FALSE;
        }

        // Exécute la requête
        return $this->database->db()
            ->where($field->getName(), $value)
            ->delete($this->config->getTable());
    }

    /**
     * Efface un ou plusieurs modèle
     * @return boolean
     */
    public function delete()
    {
        return $this->database->db()
            ->delete($this->config->getTable());
    }

}

/* End of file Orm_entity.php */
/* Location: ./application/libraries/Orm_entity.php */
