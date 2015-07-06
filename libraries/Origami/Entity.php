<?php

namespace Origami;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Entity extends Common
{
    
    protected $_config;
    
    protected $_query;

    /**
     *
     * @var \Origami\Entity\Data\Storage
     */
    protected $_storage;

    /**
     *
     * @var \Origami\Entity\Association
     */
    protected $_association;

    /**
     *
     * @var \Origami\Entity\Validator
     */
    protected $_validator;

    /**
     * Constructeur
     * @param NULL|integer|\Origami\Entity\Schema\Association $data
     */
    function __construct($data = NULL)
    {
        // Gestinnaire de configuation
        $this->_config = new \Origami\Entity\Config(self::getClass());
        
        // Gestinnaire de query
        $this->_query = new \Origami\Entity\Db\Query($this->_config);

        // Gestionnaire de stockage
        $this->_storage = new \Origami\Entity\Data\Storage($this->_config);

        // Gestionnaire d'association
        $this->_association = new \Origami\Entity\Association($this->_config, $this->_storage);

        // Gestionnaire de validation
        $this->_validator = new \Origami\Entity\Validator($this->_config, $this->_storage);

        if ($data !== NULL) {
            // Si la variable $data est un entier, c'est une clé primaire
            if (is_numeric($data)) {
                // Récupère l'objet grêce à la clé primaire
                $object = $this->_query->setPrimaryKey(new \Origami\Entity\Shema\Primarykey($this->_config), $data)->find_one();

                // Si l'objet est trouvé
                if ($object instanceof \Origami\Entity) {
                    // Insère les donnée en silence
                    $this->_storage->set($object->getToArray(), NULL, TRUE);
                }

                // Si la variable $data est une instance de la classe Orm_association
            } else if ($data instanceof \Origami\Entity\Shema\Association) {
                $this->_query->setAssociation($data);

                // Si la variable $data est un tableau
            } else if (is_array($data) && !empty($data)) {
                $this->_storage->set($data);
            }
        }
    }

    /**
     * Si un champ est définie
     * @param type $name
     * @return boolean
     */
    public function __isset($name)
    {
        return ($this->_storage->getValue($name) !== FALSE);
    }

    /**
     * Récupère la valeur d'un champ
     * @param type $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_storage->getValue($name);
    }

    /**
     * Modifie la valeur d'un champ
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_storage->set($name, $value);
    }

    /**
     * Passage d'objets en appelant la methode du nom de la relation
     * @param string $name
     * @param array $arguments
     * @return Orm_entity
     */
    public function __call($name, $arguments = array())
    {
        try {
            if (($association = $this->_association->get($name)) !== FALSE) {
                // Retoune le nouveau modèle associé
                $entity = $association->associated();
                
                return $entity->getQuery();

                // Sinon, il y a une erreur
            } else {
                throw new Exception("L'association $name est introuvable dans le modèle ".self::getClass().PHP_EOL);
            }
        } catch (Exception $exception) {
            exit("Origami a rencontré un problème : {$exception->getMessage()}");
        }
    }

    public function getToArray($index = NULL)
    {
        return $this->_storage->getValue($index);
    }

    public function getToJson($index = NULL)
    {
        return json_encode($this->_storage->getValue($index));
    }
    
    public function getConfig()
    {
        return $this->_config;
    }
    
    public function getQuery()
    {
        return $this->_query;
    }
    
    public function getStorage()
    {
        return $this->_storage;
    }

    /**
     * Valide un modèle et retourne ses erreurs
     * @return array|boolean
     */
    public function validate()
    {
        return $this->_validator->validate();
    }

    /**
     * Si un modèle est valide
     * @return boolean
     */
    public function is_valid()
    {
        return $this->_validator->is_valid();
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
        $field = $this->_storage->get($this->_config->getPrimaryKey());
        $field_value = $field->getValue();

        // Si la la requête doit être de type INSERT
        $has_insert = (empty($field_value) || $force_insert === TRUE);
                
        // Si il y a pas de changement
        if ($this->_storage->isUpdate() === FALSE) {
            return FALSE;
        }

        // Etat de la requête
        $query = FALSE;

        // Si la requete est de type replace
        if ($replace) {
            // Exécute la requête
            $query = $this
                ->write()
                ->from($this->_config->getTable())
                ->replace();

            // Si l'insertion est correcte
            if ($query === TRUE) {
                // Met a jour la clé primaire en silence
                $this->_storage->set($field->getName(), $this->db()->insert_id(), TRUE);
            }

            // Si la requete est de type insert
        } else if ($has_insert) {
            // Exécute la requête
            $query = $this
                ->write()
                ->from($this->_config->getTable())
                ->insert();

            // Si l'insertion est correcte
            if ($query === TRUE) {
                // Met a jour la clé primaire en silence
                $this->_storage->set($field->getName(), $this->db()->insert_id(), TRUE);
            }

            // Si la requete est de type update
        } else {
            // Exécute la requête
            $query = $this
                ->write()
                ->from($this->_config->getTable())
                ->where($field->getName(), $field->getValue())
                ->update();
        }

        // Vide les changements
        $this->_storage->cleanUpdate();

        // La requête a échoué
        return $query;
    }

    /**
     * Efface un modèle l'instance en cours
     * @return boolean
     */
    public function remove()
    {
        $field = $this->_storage->get($this->_config->getPrimaryKey());
        $value = $field->getValue();

        // Si la valeur est vide
        if (empty($value)) {
            return FALSE;
        }

        // Exécute la requête
        return $this->db()
            ->where($field->getName(), $value)
            ->delete($this->_config->getTable());
    }

    private function db()
    {
        return \Origami\Database::link($this->_config->getDataBase());
    }

    /**
     * Requête d'écruture
     */
    private function write()
    {
        // Les champs à mettre à jour
        $fields = $this->_storage->getUpdate();

        // Si il y a des champs a updater        
        if (!empty($fields)) {
            // Champs a mettre à jour
            foreach ($fields as $field) {
                // Si le cryptage est activé et qu'il y a des champs crypté
                if ($this->_config->getOrigami('encryption_enable') && $field->getEncrypt()) {
                    // Récupération du champ vecteur
                    $vector = $this->_storage->get('vector');
                    $value = $vector->getValue();

                    // Si le vecteur n'a pas de valeur
                    if (empty($value)) {
                        // Créer un vecteur
                        $this->_storage->set('vector', random_string('unique'));

                        // Recharge l'object le vecteur
                        $vector = $this->_storage->get('vector');

                        // Prépare l'insertion vecteur
                        $this->db()->set($vector->getName(), $vector->getValue(), TRUE);
                    }

                    // Encryptage de la valeur
                    $this->db()->set("`{$field->getName()}`", "TO_BASE64(AES_ENCRYPT('{$this->db()->escape_str($field->getValue())}', UNHEX('{$this->_config->getOrigami('encryption_key')}'), UNHEX('{$vector->getValue()}')))", FALSE);

                    // Si le champ est un binaire 
                } else if ($this->_config->getOrigami('binary_enable') && $field->getBinary()) {

                    // Transformation de la valeur
                    $this->db()->set("`{$field->getName()}`", "FROM_BASE64('{$this->db()->escape_str($field->getValue())}')", FALSE);

                    // Si c'est un champ normal
                } else {
                    $this->db()->set($field->getName(), $field->getValue(), TRUE);
                }
            }
        }

        return $this->db();
    }

}

/* End of file Orm_entity.php */
/* Location: ./application/libraries/Orm_entity.php */
