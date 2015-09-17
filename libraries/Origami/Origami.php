<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(__DIR__.'/DB.php');
require(__DIR__.'/Entity.php');
require(__DIR__.'/Entity/Manager/Config.php');
require(__DIR__.'/Entity/Manager/Query.php');
require(__DIR__.'/Entity/Manager/Storage.php');
require(__DIR__.'/Entity/Manager/Association.php');
require(__DIR__.'/Entity/Manager/Validator.php');
require(__DIR__.'/Entity/Shema/Association.php');
require(__DIR__.'/Entity/Shema/Field.php');
require(__DIR__.'/Entity/Shema/PrimaryKey.php');
require(__DIR__.'/Entity/Shema/Table.php');
require(__DIR__.'/Entity/Shema/Validation.php');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Origami
{
    /**
     * Instance de Codeigniter
     * @var stdClass $CI
     */
    private $CI;

    /**
     * Version
     * @var string $version
     */
    private $version = '0.0.10';

    /**
     * Configuration de l'ORM
     * @var array $config
     */
    private $config = array(
        'entity_autoload' => TRUE,
        'entity_path' => NULL,
        'binary_enable' => FALSE, // MySQL 5.6 minimum
        'encryption_enable' => FALSE, // MySQL 5.6 minimum
        'encryption_key' => NULL // MySQL 5.6 minimum
    );

    /**
     * Constructeur
     * @param array $config
     */
    function __construct(array $config = array())
    {
        // Charge l'instance de CodeIgniter
        $this->CI = & get_instance();

        // Initialise la configuration, si elle existe
        if (isset($config)) {
            // Si il y a une dimention en plus
            if (isset($config['origami'])) {
                $config = $config['origami'];
            }

            $this->config = array_merge($this->config, $config);
        }

        // Charge le fichier langue
        $this->CI->load->language('origami');

        // Si la clé de cryptage est vide, on désactive le cryptage
        if ($this->config['encryption_enable'] && empty($this->config['encryption_key'])) {
            $this->config['encryption_enable'] = FALSE;
        }

        // Autoloader
        if ($this->config['entity_autoload']) {
            $this->CI->load->helper('origami');
        }

        // Si le cryptage est actif charge les éléments indispensable au cryptage
        if ($this->config['encryption_enable']) {
            $this->CI->load->helper('string');
        }
    }

    /**
     * Retourne la configuration
     * @return array
     */
    public function getConfig($index = NULL)
    {
        // Si l'index existe
        if (isset($this->config[$index])) {
            return $this->config[$index];
        }

        // Retourne toute la configuration
        return $this->config;
    }

    /**
     * Retourne la version
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

}

/* End of file Origami.php */
/* Location: ./libraries/Origami/Origami.php */
