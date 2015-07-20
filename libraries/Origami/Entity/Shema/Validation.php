<?php

namespace Origami\Entity\Shema;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Validation
{
    const OPTION_TYPE_EMAIL = 'email';
    const OPTION_TYPE_URL = 'url';
    const OPTION_TYPE_IP = 'ip';
    const OPTION_TYPE_INT = 'int';
    const OPTION_TYPE_FLOAT = 'float';
    const OPTION_TYPE_EXCLUSION = 'exclusion';
    const OPTION_TYPE_INCLUSION = 'inclusion';
    const OPTION_TYPE_FORMAT = 'format';
    const OPTION_TYPE_LENGTH = 'length';
    const OPTION_TYPE_PRESENCE = 'presence';
    const OPTION_TYPE_CALLBACK = 'callback';
    const OPTION_MIN = 'min';
    const OPTION_MAX = 'max';
    const OPTION_LIST = 'list';
    const OPTION_MATCHER = 'matcher';
    const OPTION_CALLBACK = 'callback';
    const OPTION_MESSAGE = 'message';

    /**
     * Instance de CodeIgniteur
     * @var stdClass 
     */
    public $CI;

    /**
     * Nom du champs
     * @var string 
     */
    public $field;

    /**
     * Règle
     * @var string 
     */
    public $type;
    
    /**
     * Nombre minimum de caractère
     * @var integer 
     */
    public $min;
    
    /**
     * Nombre maximum de caractère
     * @var integer 
     */
    public $max;
    
    /**
     * Liste de valeur
     * @var array 
     */
    public $list;
    
    /**
     * Expression régulière
     * @var string 
     */
    public $matcher;
    
    /**
     * Function externe
     * @var string 
     */
    public $callback;
    
    /**
     * Message d'erreur
     * @var string 
     */
    public $message;
    
    /**
     * Constructeur
     * @param array $config
     */
    public function __construct(array $config)
    {
        // Instance de CodeIgniter
        $this->CI = &get_instance();

        // Si le paramètre existe
        if (property_exists($this, $config_key)) {
            $this->{$config_key} = $config_value;
        }
    }

    /**
     * Vérifie si la valeur est une adresse email
     * @param mixed $value
     * @return boolean
     */
    private function check_email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Vérifie si la valeur est une url
     * @param mixed $value
     * @return boolean
     */
    private function check_url($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

    /**
     * Vérifie si la valeur est une adress ip
     * @param mixed $value
     * @return boolean
     */
    private function check_ip($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP);
    }

    /**
     * Vérifie si la valeur est un entier
     * @param mixed $value
     * @return boolean
     */
    private function check_int($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    /**
     * Vérifie si la valeur est un nombre flotant
     * @param mixed $value
     * @return boolean
     */
    private function check_float($value)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * Vérifie si la valeur est exclut d'une liste
     * @param mixed $value
     * @return boolean
     */
    private function check_exclusion($value)
    {
        if (!is_array($this->list)) {
            return FALSE;
        }

        return !in_array($value, $this->list);
    }

     /**
     * Vérifie si la valeur est inclut d'une liste
     * @param mixed $value
     * @return boolean
     */
    private function check_inclusion($value)
    {
        if (!is_array($this->list))  {
            return FALSE;
        }

        return in_array($value, $this->list);
    }

    /**
     * Vérifie si la valeur est exclut d'une liste
     * @param mixed $value
     * @return boolean
     */
    private function check_format($value)
    {
        if (empty($this->matcher)) {
            return FALSE;
        }

        return preg_match($this->matcher, $value);
    }

    /**
     * Vérifie si la valeur est une date
     * @param mixed $value
     * @return boolean
     */
    private function check_date($value)
    {
        return checkdate(date('m', strtotime($value)), date('d', strtotime($value)), date('Y', strtotime($value)));
    }

    /**
     * Vérifie si la valeur est compris entre un min et un max
     * @param mixed $value
     * @return boolean
     */
    private function check_length($value)
    {
        if (empty($value)) {
            return FALSE;
        }

        $length = strlen($value);

        if (($this->min && $length < $this->min) || ($this->max && $length > $this->max)) {
            return FALSE;
        } else {
            return $value;
        }
    }

    /**
     * Vérifie si la valeur est présente
     * @param mixed $value
     * @return boolean
     */
    private function check_presence($value)
    {
        if (empty($value)) {
            return FALSE;
        } else {
            return $value;
        }
    }

    /**
     * Vérifie la valeur par une fonction externe
     * @param mixed $value
     * @return boolean
     */
    private function check_callback($value)
    {
        return call_user_func_array(array($this->callback), array($value, &$this));
    }

    /**
     * Validation d'un champ
     * @param \Origami\Entity\Shema\Field $field
     * @return boolean
     */
    public function validateField(\Origami\Entity\Shema\Field $field)
    {
        if (call_user_func_array(array($this, "check_{$this->getType()})"), array($field->getValue())) === FALSE) {

            if ($message = $this->getMessage() && empty($message)) {
                $this->setMessage($this->CI->lang->line("orm_validation_{$this->getType()}"));
            }

            return FALSE;
        }

        return TRUE;
    }

}

/* End of file Validation.php */
/* Location: ./libraries/Origami/Entity/Shema/Validation.php */
