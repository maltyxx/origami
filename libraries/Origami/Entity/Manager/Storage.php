<?php

namespace Origami\Entity\Manager;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Storage
{
    /**
     * Liste des champs
     * @var array $fields
     */
    private $fields = array();

    /**
     * Liste des champs modifiés
     * @var array $modified
     */
    private $modified = array();

    /**
     * Si une entité a été Sauvegardée
     * @var boolean
     */
    private $new = FALSE;

    /**
     * Constructeur
     * @param \Origami\Entity\Manager\Config $config
     */
    public function __construct(\Origami\Entity\Manager\Config $config)
    {
        // Instance du gestionnaire de configuration
        $this->setConfig($config);
    }

    /**
     * Recherche un ou plusieurs champs
     * @param string|NULL $index
     * @param boolean $dirty
     * @return array|\Origami\Entity\Shema\Field|boolean
     */
    public function get($index = NULL, $dirty = FALSE)
    {
        if ($dirty === TRUE) {
			if ($index === NULL) {
				return $this->modified;
			} else if (isset($this->modified[$index])) {
				return $this->modified[$index];
			} else {
				return FALSE;
			}
		} else {
			if ($index === NULL) {
				return $this->fields;
			} else if (isset($this->fields[$index])) {
				return $this->fields[$index];
			} else {
				return FALSE;
			}
		}
    }

    /**
     * Modifie la valeur d'un ou de plusieurs champss
     * @param string|NULL $index
     * @param mixed $value
     * @param boolean $silence
     */
    public function set($index, $value = NULL, $silence = FALSE)
    {
        // Si l'index est un tableau
        if (is_array($index)) {
            foreach ($index as $key => $value) {
                $this->set($key, $value, $silence);
            }
            // Si l'index n'est pas un tableau
        } else if (isset($this->fields[$index])) {
            $this->fields[$index]->setValue($value, $silence);
            
            // Si le mode silence est désactivé et si la valeur a changé
            if ($silence === FALSE && $this->fields[$index]->dirty()) {
				// Passe le champ en dirty
                $this->fields[$index]->setDirty(TRUE);

				// Lie les champs
                $this->modified[$index] =& $this->fields[$index];
            }
        }
    }

    /**
     * Si l'entité est nouvelle instance
     * @return boolean
     */
    public function isNew($new = NULL)
    {
        if ($new !== NULL) {
            return $this->new = $new;
        }

        return $this->new;
    }
	
    /**
     * Vérifie si l'entité a changé
     * @param type $index
     * @return array|\Origami\Entity\Shema\Field|boolean
     */
    public function dirty($index = NULL, $force = FALSE)
    {
        // Vérifie l'entité a changé
        if ($index === NULL) {
            return !empty($this->modified);

        // Vérifie si le champs a changé
        } else if (isset($this->modified[$index]) && $force === FALSE) {
            return isset($this->modified[$index]);

        // Marque le champ comme modifié
        } else if (isset($this->fields[$index]) && $force === TRUE) {
            $this->fields[$index]->setDirty(TRUE);
            return TRUE;

        // Autrement
        } else {
            return FALSE;
        }
    }

    /**
     * Efface les champs modifiés
     * @return boolean
     */
    public function clean()
    {
        if (!empty($this->modified)) {
            return FALSE;
        }

        foreach ($this->modified as $name) {
            $this->fields[$name]->setDirty(FALSE);
        }

        $this->modified = array();

        return TRUE;
    }

    /**
     * Recherche la valeur d'un ou plusieurs champs
     * @param type $index
     * @return mixed
     */
    public function value($index = NULL)
    {
        if ($index === NULL) {
            $fields = array();

            foreach ($this->fields as $field) {
                $fields[$field->getName()] = $field->getValue();
            }

            return $fields;

        } else if (isset($this->fields[$index])) {
            return $this->fields[$index]->getValue();

        } else {
            return FALSE;
        }
    }

    /**
     * Renseigne le gestionnaire d'erreur
     * @param \Origami\Entity\Manager\Config $config
     */
    private function setConfig(\Origami\Entity\Manager\Config &$config)
    {
        foreach ($config->getField() as $field) {
            $this->fields[$field['name']] = new \Origami\Entity\Shema\Field($field, NULL, TRUE);
        }
    }

}

/* End of file Storage.php */
/* Location: ./libraries/Origami/Entity/Manager/Storage.php */
