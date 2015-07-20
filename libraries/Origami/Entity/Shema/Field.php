<?php

namespace Origami\Entity\Shema;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Field
{
    const TYPE_INTEGER = 'int';
    const TYPE_INT = self::TYPE_INTEGER;
    const TYPE_FLOAT = 'float';
    const TYPE_DOUBLE = self::TYPE_FLOAT;
    const TYPE_STRING = 'string';
    const TYPE_DATE = 'date';
    const DATEFORMAT = 'Y-m-d H:i:s';
    const ALLOWNULL = 'allow_null';
    const ENCRYPT = 'encrypt';
    const BINARY = 'binary';
    const DEFAULTVALUE_NOW = 'now';

    /**
     * Nom du champ
     * @var string 
     */
    private $name;

    /**
     * Type de champ
     * @var string 
     */
    private $type;

    /**
     * Nom du champ
     * @var string 
     */
    private $date_format;

    /**
     * Si le champ peut être NULL
     * @var boolean 
     */
    private $allow_null;

    /**
     * Si le champ est crypté
     * @var boolean 
     */
    private $encrypt;

    /**
     * Si le champ est un binaire
     * @var boolean 
     */
    private $binary;

    /**
     * La valeur par defaut du champ
     * @var mixed 
     */
    private $default_value;

    /**
     * La valeur du champ
     * @var mixed 
     */
    private $_value;

    /**
     * L'ancienne valeur du champ
     * @var mixed 
     */
    private $_oldvalue;

    /**
     * Si il y a eu une modification de valeur
     * @var boolean
     */
    private $_dirty = FALSE;

    /**
     * Constructeur
     * @param array $config
     * @param mixed $value
     * @param boolean $silence
     */
    public function __construct(array $config, $value = NULL, $silence = FALSE)
    {
        // Parcours la configuration
        foreach ($config as $config_key => $config_value) {
            // Si le paramètre existe
            if (property_exists($this, $config_key)) {
                $this->{$config_key} = $config_value;
			}
        }
		
        // Change la valeur du champ
        $this->setValue($value, $silence);
        
        // Si le type n'est pas défini
        if (empty($this->type)) {
            $this->type = self::TYPE_STRING;
        }
        
        // Si le type est une date
        if ($this->type === self::TYPE_DATE && empty($this->date_format)) {
            $this->date_format = self::DATEFORMAT;
        } else if (empty($this->date_format)) {
            $this->date_format = FALSE;
        }
        
        // Si le champs doit être crypté
        if (empty($this->encrypt)) {
            $this->encrypt = FALSE;
        }

        if (empty($this->binary)) {
            $this->binary = FALSE;
        }

        if (empty($this->allow_null)) {
            $this->allow_null = FALSE;
        }

        if (empty($this->default_value)) {
            $this->default_value = FALSE;
        }
    }

    /**
     * Retourne le nom
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Retourne le type
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Retourne le format de la date
     * @return string
     */
    public function getDateFormat()
    {
        return $this->date_format;
    }

    /**
     * Si le champ peut avoir une valeur à null
     * @return string
     */
    public function getAllowNull()
    {
        return $this->allow_null;
    }

    /**
     * Si le champ est crypté
     * @return string
     */
    public function getEncrypt()
    {
        return $this->encrypt;
    }
    
    /**
     * Si le champ est un binaire
     * @return string
     */
    public function getBinary()
    {
        return $this->binary;
    }

    /**
     * Retourne la valeur par défaut
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * Retourne la valeur
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Modifie la valeur du champ
     * @param type $value Valeur
     * @param type $silence Mode silence
     */
    public function setValue($value, $silence = FALSE)
    {
        // Cast la valeur
        $value = $this->convert($value);

        // Si la valeur est différente
        if ($value !== $this->_value) {
            // Sauvegarde l'ancienne valeur
            $this->_oldvalue = $this->_value;

            // Met a jour la nouvelle valeur
            $this->_value = $value;

            // Si le mode silence est désactivé
            if ($silence === FALSE) {
                // Indique que le champ a été mis à jour
                $this->_dirty = TRUE;
            }
        }
    }
    
    /**
     * Si le champ a été modifié
     * @return boolean
     */
    public function dirty()
    {
        return $this->_dirty;
    }

    /**
     * Change l'état du champ
     * @param boolean $value
     */
    public function setDirty($value)
    {
        $this->_dirty = $value;
    }

    /**
     * Convertie une valeur
     * @param mixe $value Valeur
     * @return mixe $value Valeur
     */
    public function convert($value)
    {
        // Si le champ a une valeur par défaut
        if (!empty($this->default_value) && empty($value)) {
            if ($this->type === self::TYPE_DATE && $this->default_value === self::DEFAULTVALUE_NOW) {
                return date($this->date_format);
            } else {
                return $this->default_value;
            }
        }

        // Si le champ peut être null
        if ($this->allow_null === TRUE && $value == '') {
            return NULL;
        }

        // Si le champ est de type date
        if ($this->type === self::TYPE_DATE && !empty($value)) {            
            // Si la valeur est un objet DateTime
            if ($value instanceof \DateTime)  {
                return $value->format($this->date_format);
            } else {
                return date($this->date_format, strtotime($value));
            }
        }

        // Cast la valeur
        switch (strtolower($this->type)) {
            case self::TYPE_INTEGER:
                settype($value, 'integer');
                break;
            case self::TYPE_FLOAT:
                settype($value, 'float');
                break;
            default:
                settype($value, 'string');
        }

        return $value;
    }

}

/* End of file Field.php */
/* Location: ./libraries/Origami/Entity/Shema/Field.php */
