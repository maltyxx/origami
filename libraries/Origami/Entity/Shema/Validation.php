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
    public $min;
    public $max;
    public $list;
    public $matcher;
    public $callback;
    public $message;

    public function __construct(array $config)
    {
        // Instance de CodeIgniter
        $this->CI = & get_instance();

        foreach ($config as $config_key => $config_value) {
            $this->{$config_key} = $config_value;
        }
    }

    private function check_email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    private function check_url($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

    private function check_ip($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP);
    }

    private function check_int($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    private function check_float($value)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    private function check_exclusion($value)
    {
        if (!is_array($this->list))
            return FALSE;

        return !in_array($value, $this->list);
    }

    private function check_inclusion($value)
    {
        if (!is_array($this->list))
            return FALSE;

        return in_array($value, $this->list);
    }

    private function check_format($value)
    {
        if (empty($this->matcher))
            return FALSE;

        return preg_match($this->matcher, $value);
    }

    private function check_date($value)
    {
        return checkdate(date('m', strtotime($value)), date('d', strtotime($value)), date('Y', strtotime($value)));
    }

    private function check_length($value)
    {
        if (empty($value))
            return FALSE;

        $length = strlen($value);

        if (($this->min && $length < $this->min) || ($this->max && $length > $this->max)) {
            return FALSE;
        } else {
            return $value;
        }
    }

    private function check_presence($value)
    {
        if (empty($value)) {
            return FALSE;
        } else {
            return $value;
        }
    }

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

/* End of file Orm_validation.php */
/* Location: ./application/libraries/Orm_validation.php */
