<?php

namespace Origami\Entity;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Entity extends \Origami\Entity\Core
{
    /**
     * Gestionnaire de configuration
     * @var \Origami\Entity\Manager\Config 
     */
    protected $_config;
    
    /**
     * Gestionnaire de requête
     * @var \Origami\Entity\Manager\Query
     */
    protected $_query;

    /**
     * Gestionnaire de stockage
     * @var \Origami\Entity\Manager\Storage
     */
    protected $_storage;

    /**
     * Gestionnaire de relation
     * @var \Origami\Entity\Manager\Association
     */
    protected $_association;

    /**
     * Gestionnaire de validation
     * @var \Origami\Entity\Manager\Validator
     */
    protected $_validator;

    /**
     * Constructeur
     * @param NULL|integer|\Origami\Entity\Schema\Association $data
     */
    function __construct($data = NULL)
    {
        // Gestinnaire de configuation
        $this->_config = new \Origami\Entity\Manager\Config(self::entity());
        
        // Gestinnaire de query
        $this->_query = new \Origami\Entity\Manager\Query($this->_config);

        // Gestionnaire de stockage
        $this->_storage = new \Origami\Entity\Manager\Storage($this->_config);

        // Gestionnaire d'association
        $this->_association = new \Origami\Entity\Manager\Association($this->_config, $this->_storage);

        // Gestionnaire de validation
        $this->_validator = new \Origami\Entity\Manager\Validator($this->_config, $this->_storage);

        // Si la variable $data est un entier, c'est une clé primaire
        if (is_numeric($data)) {
            // Récupère l'objet grêce à la clé primaire
            $object = $this->_query->setPrimaryKey(new \Origami\Entity\Shema\Primarykey($this->_config), $data)->find_one();

            // Si l'objet est trouvé
            if ($object instanceof \Origami\Entity\Entity) {
                // Insère les donnée en silence
                $this->_storage->set($object->getToArray(), NULL, TRUE);
            }

            // Si la variable $data est une instance \Origami\Entity\Shema\Association
        } else if ($data instanceof \Origami\Entity\Shema\Association) {
            $this->_query->setAssociation($data);

            // Si la variable $data est un tableau
        } else if (is_array($data) && !empty($data)) {
            $this->_storage->set($data);
        }
        
    }

    /**
     * Détermine si un attribut est définie et est différente de NULL
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return ($this->_storage->getValue($name) !== FALSE);
    }

    /**
     * Retourne la valeur d'un attribut
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_storage->getValue($name);
    }

    /**
     * Modifie la valeur d'un attribut
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_storage->set($name, $value);
    }

    /**
     * Retourne la relation avec un autre modèle
     * @param string $name
     * @param array $arguments
     * @return Entity\Db\Query
     */
    public function __call($name, $arguments = array())
    {
        try {
            // 
            if (($association = $this->_association->get($name)) !== FALSE) {                
                // Créer une association
                $entity = $association->associated();
                
                // Retourne le gestionnaire de requête 
                return $entity->getQuery();

                // Sinon, il y a une erreur
            } else {
                throw new Exception("L'association $name est introuvable dans le modèle ".self::entity().PHP_EOL);
            }
        } catch (Exception $exception) {
            exit("Origami a rencontré un problème : {$exception->getMessage()}");
        }
    }
    
    /**
     * Retourne les résultats dans un tableau associatif
     * @param string $index
     * @return array
     */
    public function getToArray($index = NULL)
    {
        return $this->_storage->getValue($index);
    }
    
    /**
     * Retourne les résultats au format JSON
     * @param string $index
     * @return array
     */
    public function getToJson($index = NULL)
    {
        return json_encode($this->_storage->getValue($index));
    }
    
    /**
     * Retourne le gestionnaire de configuration
     * @return Entity\Config
     */
    public function getConfig()
    {
        return $this->_config;
    }
    
    /**
     * Retourne le gestionnaire de requête
     * @return Entity\Db\Query
     */
    public function getQuery()
    {
        return $this->_query;
    }
    
    /**
     * Retourne le gestionnaire de stockage
     * @return Entity\Data\Storage
     */
    public function getStorage()
    {
        return $this->_storage;
    }

    /**
     * Lance la validation et retourne ses erreurs
     * @return array|boolean
     */
    public function validate()
    {
        return $this->_validator->validate();
    }

    /**
     * Vérifie si les données de l'entité sont valides
     * @return boolean
     */
    public function is_valid()
    {
        return $this->_validator->is_valid();
    }

    /**
     * Sauvegarde les valeurs de l'entité en base de donnée
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
     * Efface l'entité en base de donnée
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
    
    /**
     * Retourne l'object db
     * @return type
     */
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

/* End of file Entity.php */
/* Location: ./libraries/Origami/Entity/Entity.php */
