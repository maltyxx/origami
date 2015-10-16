<?php

namespace Origami\Entity\Core;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Entity extends Core implements EntityInterface
{
    /**
     * Les pluggings
     * @var array
     */
    protected $_pluggins = array();

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
        return ($this->getPluggin('storage')->value($name) !== FALSE);
    }

    /**
     * Retourne la valeur d'un attribut
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getPluggin('storage')->value($name);
    }

    /**
     * Modifie la valeur d'un attribut
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        // // Si l'entité n'est pas initialisé (Ex: mysqli_fetch_object)
        if ($this->getPluggin('config') instanceof \Origami\Entity\Manager\Config) {
            $this->initialize();
        }

        $this->getPluggin('storage')->set($name, $value);
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
        if ($this->getPluggin('config') instanceof \Origami\Entity\Manager\Config) {
            return;
        }

        // Options
        $options = array_merge(array(
            'new' => TRUE,
            'silence' => FALSE
        ), $options);
        
        // Charge le pluggin de configuration
        $this->setPluggin(new \Origami\Plugging\Config());

        // Charge le plugging de requête
        $this->setPluggin(new \Origami\Plugging\Query());

        // Charge le plugging de stockage
        $this->setPluggin(new \Origami\Plugging\Storage());
        
        // Charge le pluggin association
        $this->setPluggin(new \Origami\Plugging\Association());
        
        // Charge le pluggin valudator
        $this->setPluggin(new \Origami\Plugging\Validator());

        // Si c'est une nouvelle instance
        $this->getPluggin('storage')->isNew($options['new']);

        // Si la variable $data est un entier, c'est une clé primaire
        if (is_numeric($data)) {
            // Récupère l'objet grêce à la clé primaire
            $object = $this->_query->setPrimaryKey(new \Origami\Entity\Shema\Primarykey($this->getPluggin('config')), $data)->find_one();

            // Si l'objet est trouvé
            if ($object instanceof \Origami\Entity) {
                // Indique que ce n'est pas une nouvelle instance
                $this->getPluggin('storage')->isNew(FALSE);

                // Insère les donnée en silence
                $this->getPluggin('storage')->set($object->toArray(), NULL, TRUE);
            }

            // Si la variable $data est une instance \Origami\Entity\Shema\Association
        } else if ($data instanceof \Origami\Entity\Shema\Association) {
            $this->_query->setAssociation($data);
        // Gestinnaire de configuation
        $this->getPluggin('config') = new \Origami\Entity\Manager\Config(self::entity());
            // Si la variable $data est un tableau
        } else if (is_array($data) && !empty($data)) {
            $this->getPluggin('storage')->set($data, NULL, $options['silence']);
        }
    }

    /**
     * Retourne les résultats dans un tableau associatif
     * @param string $index
     * @return array
     */
    public function get($index = NULL)
    {
        return $this->getPluggin('storage')->value($index);
    }

    /**
     * Modifie un ou plusieurs champs
     * @param type $index
     * @param type $value
     * @param boolean $silence
     */
    public function set($index, $value = NULL, $silence = FALSE) {
       $this->getPluggin('storage')->set($index, $value, $silence);
    }

    /**
     * Retourne les résultats dans un tableau associatif
     * @param string $index
     * @return array
     */
    public function toArray()
    {
        return $this->getPluggin('storage')->value();
    }

    /**
     * Retourne les résultats au format JSON
     * @param string $index
     * @return array
     */
    public function toJson()
    {
        return json_encode($this->getPluggin('storage')->value());
    }
    

    /**
     * Lance la validation et retourne ses erreurs
     * @return array|boolean
     */
    public function validate()
    {
        return $this->getPluggin('validator')->validate();
    }

    /**
     * Vérifie si les données de l'entité sont valides
     * @return boolean
     */
    public function isValid()
    {
        return $this->getPluggin('validator')->is_valid();
    }

    /**
     * Vérifie si l'entité a changé
     * @param type $index
     * @return boolean
     */
    public function dirty($index = NULL, $force = FALSE)
    {
        return $this->getPluggin('storage')->dirty($index, $force);
    }

    /**
     * Efface les champs modifiés
     * @return boolean
     */
    public function clean($index = NULL)
    {
        return $this->getPluggin('storage')->clean($index);
    }

    /**
     * Vérifie si une entité a été Sauvegardée
     * @return boolean
     */
    public function isNew($force = NULL)
    {
        return $this->getPluggin('storage')->isNew($force);
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
        $field = $this->getPluggin('storage')->get($this->getPluggin('config')->getPrimaryKey());
        $field_value = $field->getValue();

        // Si la la requête doit être de type INSERT
        $has_insert = (empty($field_value) || $options['force_insert'] === TRUE);
        
        // Si il y a pas de changement
        if ($this->getPluggin('storage')->dirty() === FALSE) {
            return FALSE;
        }

        // Etat de la requête
        $query = FALSE;

        // Si la requete est de type replace
        if ($options['replace']) {
            // Exécute la requête
            $query = $this
                ->write()
                ->from($this->getPluggin('config')->getTable())
                ->replace();

            // Si l'insertion est correcte
            if ($query === TRUE) {
                // Met a jour la clé primaire en silence
                $this->getPluggin('storage')->set($field->getName(), $this->db()->insert_id(), TRUE);
            }

            // Si la requete est de type insert
        } else if ($has_insert === TRUE) {
            // Exécute la requête
            $query = $this
                ->write()
                ->from($this->getPluggin('config')->getTable())
                ->insert();

            // Si l'insertion est correcte
            if ($query === TRUE) {
                // Met a jour la clé primaire en silence
                $this->getPluggin('storage')->set($field->getName(), $this->db()->insert_id(), TRUE);
            }

            // Si la requete est de type update
        } else {
            // Exécute la requête
            $query = $this
                ->write()
                ->from($this->getPluggin('config')->getTable())
                ->where($field->getName(), $field->getValue())
                ->update();
        }
        
        // Change le status de l'entité
        if ($query === TRUE && $this->getPluggin('storage')->isNew()) {
            $this->getPluggin('storage')->isNew(FALSE);
        }

        // Vide les changements
        $this->getPluggin('storage')->clean();

        // La requête a échoué
        return $query;
    }

    /**
     * Efface l'entité en base de donnée
     * @return boolean
     */
    public function remove()
    {
        $config = $this->getPluggin('config');
        $field = $this->getPluggin('storage')->get($config->getPrimaryKey());
        $value = $field->getValue();

        // Si la valeur est vide
        if (empty($value)) {
            return FALSE;
        }

        // Exécute la requête
        return $this->db()
            ->where($field->getName(), $value)
            ->delete($config->getTable());
    }
    
    /**
     * Retourne l'object db
     * @return type
     */
    private function db()
    {
        return \Origami\DB::get($this->getPluggin('config')->getDataBase());
    }

    /**
     * Requête d'écruture
     */
    private function write()
    {
        // Liste des champs modifiés
        $fields = $this->getPluggin('storage')->get(NULL, TRUE);

        // Si il y a des champs modifiés
        if (!empty($fields)) {
            // Si le cryptage est activé et si il y a un champ vecteur
            if ($this->getPluggin('config')->getOrigami('encryption_enable') && $this->getPluggin('storage')->get('vector') !== FALSE) {
                // Récupération du champ vecteur
                $vector = $this->getPluggin('storage')->get('vector');
                $value = $vector->getValue();

                // Si le vecteur n'a pas de valeur
                if (empty($value)) {
                    // Créer un vecteur
                    $this->getPluggin('storage')->set('vector', random_string('unique'));

                    // Recharge l'object le vecteur
                    $vector = $this->getPluggin('storage')->get('vector');

                    // Prépare l'insertion vecteur
                    $this->db()->set($vector->getName(), $vector->getValue(), TRUE);
                }
            }

            // Parcours les champs modifiés
            foreach ($fields as $field) {
                // Si le cryptage est activé et qu'il y a des champs crypté
                if ($this->getPluggin('config')->getOrigami('encryption_enable') && $field->getEncrypt()) {
                    // Récupération du champ vecteur
                    $vector = $this->getPluggin('storage')->get('vector');

                    // Encryptage de la valeur
                    $this->db()->set("`{$field->getName()}`", "TO_BASE64(AES_ENCRYPT('{$this->db()->escape_str($field->getValue())}', UNHEX('{$this->getPluggin('config')->getOrigami('encryption_key')}'), UNHEX('{$vector->getValue()}')))", FALSE);

                    // Si le champ est un binaire
                } else if ($this->getPluggin('config')->getOrigami('binary_enable') && $field->getBinary()) {

                    // Transformation de la valeur
                    $this->db()->set("`{$field->getName()}`", "FROM_BASE64('{$this->db()->escape_str($field->getValue())}')", FALSE);

                    // Si c'est un champ normal
                } else {
                    $this->db()->set($field->getName(), $field->getValue(), TRUE);
                }
            }
        }
        
        // Retourne l'object
        return $this->db();
    }
    
    /**
     * Ajoute un pluggin
     * @param \Origami\Pluggin\PlugginInterface $pluggin
     */
    public function setPluggin(\Origami\Pluggin\PlugginInterface $pluggin)
    {
        // Si le plugging existe pas
        if (!isset($this->_pluggins[$pluggin->getName()])) {
            $this->_pluggins[$pluggin->getName()] = &$pluggin;
            $this->_pluggins[$pluggin->getName()]->setEntity($this);
        }
    }
    
    /**
     * Obtient le plugging demandé
     * @param string $name
     * @return \Origami\Pluggin\PlugginInterface
     */
    public function getPluggin($name)
    {        
        return isset($this->_pluggins[$name]) ? $this->_pluggins[$name] : FALSE;
    }

}

/* End of file Entity.php */
/* Location: ./libraries/Origami/Entity/Entity.php */
