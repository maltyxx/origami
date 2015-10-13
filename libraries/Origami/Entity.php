<?php

namespace Origami;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Entity
{
	/**
     * Table
     * @var string $table
     */
	public static $table;
	
    /**
     * Clé primaire
     * @var string $primary_key
     */
	public static $primary_key;
	
    /**
     * Champs
     * @var array $fields
     */
	public static $fields = array();
	
    /**
     * Associations
     * @var array $associations
     */
	public static $associations = array();
	
    /**
     * Validations
     * @var array $validations
     */
	public static $validations = array();
	
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
	 * Factory
	 * @param string $name
	 * @param array $arguments
	 * @return \Origami\Entity\Manager\Query
	 */
	public static function __callStatic($name, $arguments = array()) {

        $config = new \Origami\Entity\Manager\Config(self::entity());

		// Si c'est une requête
		if (method_exists('\Origami\Entity\Manager\Query', $name)) {

			$query = new \Origami\Entity\Manager\Query($config);

			return call_user_func_array(array($query, $name), $arguments);
		}
	}

	/**
     * Configuration général
     * @return array
     */
	public static function origami()
    {
        $CI =& get_instance();
		
		return (isset($CI->origami) && $CI->origami instanceof \Origami) ? $CI->origami->getConfig() : array();
    }
	
	/**
     * Nom de la classe
     * @return string
     */
    public static function entity()
    {
        return get_called_class();
    }

    /**
     * Nom de la base de donnée
     * @return string
     */
    public static function database()
    {
        return explode('\\', self::entity())[1];
    }
	
	/**
     * Nom de la table
     * @return string
     */
    public static function table()
    {
        return static::$table;
    }

    /**
     * Nom de la clé primaire
     * @return string
     */
    public static function primaryKey()
    {
        return static::$primary_key;
    }

    /**
     * Liste des champs
     * @return array
     */
    public static function fields()
    {
        return static::$fields;
    }

    /**
     * Liste des associations
     * @return array
     */
    public static function associations()
    {
        return static::$associations;
    }

    /**
     * liste des validateurs
     * @return array
     */
    public static function validations()
    {
        return static::$validations;
    }

    /**
     * Constructeur
     * @param NULL|integer|\Origami\Entity\Schema\Association $data
     * @param array $options
     */
    function __construct($data = NULL, $options = array())
    {
        $this->initialize($data, $options);
    }

    /**
     * Détermine si un attribut est définie et est différente de NULL
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return ($this->_storage->value($name) !== FALSE);
    }

    /**
     * Retourne la valeur d'un attribut
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_storage->value($name);
    }

    /**
     * Modifie la valeur d'un attribut
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        // // Si l'entité n'est pas initialisé (Ex: mysqli_fetch_object)
        if (!$this->_config instanceof \Origami\Entity\Manager\Config) {
            $this->initialize();
        }

        $this->_storage->set($name, $value);
    }

    /**
     * Relation avec une entité
     * @param string $name
     * @param array $arguments
     * @return Entity\Db\Query
     */
    public function __call($name, $arguments = array())
    {
        // Si il sagit d'une relation
        if (($association = $this->_association->get($name)) !== FALSE) {
            // Créer une association
            $instance = $association->associated();

            // Retourne le gestionnaire de requête
            return $instance->query();

        // Si il sagit d'une requête
        } else {
            $error = "Origami a rencontré un problème :"
                ." L'association '$name' est introuvable dans le modèle ".self::entity().", "
                ." ligne :".debug_backtrace()[1]['line']." fichier : ".debug_backtrace()[1]['file'];

            exit($error.PHP_EOL);
        }
    }
    
    /**
     * Initialisateur
     * @param NULL|integer|\Origami\Entity\Schema\Association $data
     * @param array $options
     */
    private function initialize($data = NULL, $options = array())
    {
        // Si l'entité est déjà initialisé (Ex: mysqli_fetch_object)
        if ($this->_config instanceof \Origami\Entity\Manager\Config) {
            return;
        }

        // Options
        $options = array_merge(array(
            'new' => TRUE,
            'silence' => FALSE
        ), $options);

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

        // Si c'est une nouvelle instance
        $this->_storage->isNew($options['new']);

        // Si la variable $data est un entier, c'est une clé primaire
        if (is_numeric($data)) {
            // Récupère l'objet grêce à la clé primaire
            $object = $this->_query->setPrimaryKey(new \Origami\Entity\Shema\Primarykey($this->_config), $data)->find_one();

            // Si l'objet est trouvé
            if ($object instanceof \Origami\Entity) {
                // Indique que ce n'est pas une nouvelle instance
                $this->_storage->isNew(FALSE);

                // Insère les donnée en silence
                $this->_storage->set($object->toArray(), NULL, TRUE);
            }

            // Si la variable $data est une instance \Origami\Entity\Shema\Association
        } else if ($data instanceof \Origami\Entity\Shema\Association) {
            $this->_query->setAssociation($data);

            // Si la variable $data est un tableau
        } else if (is_array($data) && !empty($data)) {
            $this->_storage->set($data, NULL, $options['silence']);
        }
    }

    /**
     * Retourne les résultats dans un tableau associatif
     * @param string $index
     * @return array
     */
    public function get($index = NULL)
    {
        return $this->_storage->value($index);
    }

    /**
     * Modifie un ou plusieurs champs
     * @param type $index
     * @param type $value
     * @param boolean $silence
     */
    public function set($index, $value = NULL, $silence = FALSE) {
        $this->_storage->set($index, $value, $silence);
    }

    /**
     * Retourne les résultats dans un tableau associatif
     * @param string $index
     * @return array
     */
    public function toArray()
    {
        return $this->_storage->value();
    }

    /**
     * Retourne les résultats au format JSON
     * @param string $index
     * @return array
     */
    public function toJson()
    {
        return json_encode($this->_storage->value());
    }
    
    /**
     * Retourne le gestionnaire de configuration
     * @return Entity\Config
     */
    public function config()
    {
        return $this->_config;
    }

    /**
     * Retourne le gestionnaire de requête
     * @return Entity\Db\Query
     */
    public function query()
    {
        return $this->_query;
    }

    /**
     * Retourne le gestionnaire de stockage
     * @return Entity\Data\Storage
     */
    public function storage()
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
    public function isValid()
    {
        return $this->_validator->is_valid();
    }

    /**
     * Vérifie si l'entité a changé
     * @param type $index
     * @return boolean
     */
    public function dirty($index = NULL, $force = FALSE)
    {
        return $this->_storage->dirty($index, $force);
    }

    /**
     * Efface les champs modifiés
     * @return boolean
     */
    public function clean($index = NULL)
    {
        return $this->_storage->clean($index);
    }

    /**
     * Vérifie si une entité a été Sauvegardée
     * @return boolean
     */
    public function isNew($force = NULL)
    {
        return $this->_storage->isNew($force);
    }

    /**
     * Sauvegarde les valeurs de l'entité en base de donnée
     * @param array $options
     * @return boolean
     */
    public function save($options = array())
    {
        // Options
        $options = array_merge(array(
            'replace' => FALSE,
            'force_insert' => FALSE
        ), $options);
        
        // Clé primaire
        $field = $this->_storage->get($this->_config->getPrimaryKey());
        $field_value = $field->getValue();

        // Si la la requête doit être de type INSERT
        $has_insert = (empty($field_value) || $options['force_insert'] === TRUE);
        
        // Si il y a pas de changement
        if ($this->_storage->dirty() === FALSE) {
            return FALSE;
        }

        // Etat de la requête
        $query = FALSE;

        // Si la requete est de type replace
        if ($options['replace']) {
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
        } else if ($has_insert === TRUE) {
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
        
        // Change le status de l'entité
        if ($query === TRUE && $this->_storage->isNew()) {
            $this->_storage->isNew(FALSE);
        }

        // Vide les changements
        $this->_storage->clean();

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
        return \Origami\DB::get($this->_config->getDataBase());
    }

    /**
     * Requête d'écruture
     */
    private function write()
    {
        // Liste des champs modifiés
        $fields = $this->_storage->get(NULL, TRUE);

        // Si il y a des champs modifiés
        if (!empty($fields)) {
            // Si le cryptage est activé et si il y a un champ vecteur
            if ($this->_config->getOrigami('encryption_enable') && $this->_storage->get('vector') !== FALSE) {
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
            }

            // Parcours les champs modifiés
            foreach ($fields as $field) {
                // Si le cryptage est activé et qu'il y a des champs crypté
                if ($this->_config->getOrigami('encryption_enable') && $field->getEncrypt()) {
                    // Récupération du champ vecteur
                    $vector = $this->_storage->get('vector');

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
