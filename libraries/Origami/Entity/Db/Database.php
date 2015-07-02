<?php

namespace Origami\Entity\Db;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Database
{
    /**
     * Instance de CodeIgniter
     * @var \stdClass 
     */
    private $CI;

    /**
     * Nom de la resource
     * @var string $group
     */
    private $group;

    /**
     * Nom de la base de donnée
     * @var string $name
     */
    private $name;

    /**
     * Constructeur
     * @param \Origami\Entity\Config $config
     */
    public function __construct(\Origami\Entity\Config &$config)
    {
        $this->initialize($config);
    }

    /**
     * Initialisateur
     * @param \Origami\Entity\Config $config
     */
    private function initialize(\Origami\Entity\Config &$config)
    {
        // Instance de CodeIgniter
        $this->CI = & get_instance();

        // Nom de la base de donnée
        $this->setName($config);

        // Nom du groupe
        $this->setGroup($config);

        // Connexion à la base de donnée
        $this->connect($config);
    }

    /**
     * Nom de la base de donnée
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Change le nom de la base de donnée
     * @param \Origami\Entity\Config $config
     */
    public function setName(\Origami\Entity\Config &$config)
    {
        $this->name = $config->getDataBase();
    }

    /**
     * Nom de la resource
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Change le nom de la resource
     * @param \Origami\Entity\Config $config
     */
    public function setGroup(\Origami\Entity\Config &$config)
    {
        $this->group = 'db_'.$config->getDataBase();
    }

    /**
     * Ouverture de la connexion
     * @param \Origami\Entity\Config $config
     */
    public function connect(\Origami\Entity\Config &$config)
    {
        // Si la connexion existe
        if (!$this->hasDb()) {
            // Charge la librairie DB
            $this->setDb($this->CI->load->database($this->getName(), TRUE));

            // Connexion à la base de donnée
            $this->getDb()->initialize();

            // Si le cryptage est actif charge les éléments indispensable au cryptage
            if ($config->getOrigami('encryption_enable') === TRUE) {
                $this->getDb()->query("SET @@session.block_encryption_mode = 'aes-256-cbc';");
            }
        }
    }

    /**
     * Fermeture le la connexion
     */
    public function close()
    {
        // Si la connexion existe
        if (!$this->hasDb()) {
            // Ferme la connexion
            $this->getDb()->close();
        }
    }

    /**
     * Retourne l'object connexion
     * @return object
     */
    public function db()
    {
        return $this->CI->{$this->getGroup()};
    }

    /**
     * Retourne l'object connexion
     * @return object
     */
    private function getDb()
    {
        return $this->db();
    }

    /**
     * Modifie l'object connexion
     * @return object
     */
    private function setDb($driver)
    {
        $this->CI->{$this->getGroup()} = $driver;
    }

    /**
     * Si l'object connexion exist
     * @return boolean
     */
    private function hasDb()
    {
        return isset($this->CI->{$this->getGroup()});
    }

}

/* End of file Orm_field.php */
/* Location: ./application/libraries/Orm_field.php */
