<?php

namespace Origami\Entity\Shema;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class PrimaryKey
{
    /**
     * Nom de la clé primaire
     * @var string $name
     */
    private $name;

    /**
     * Contructeur
     * @param \Origami\Entity\Config $config
     */
    public function __construct(\Origami\Entity\Config &$config)
    {
        $this->setName($config);
    }

    /**
     * Retourne le nom de la clé primaire
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Modifie le nom de la clé primaire
     * @param \Origami\Entity\Config $config
     */
    public function setName(\Origami\Entity\Config &$config)
    {
        $this->name = $config->getPrimaryKey();
    }

}

/* End of file PrimaryKey.php */
/* Location: ./libraries/Origami/Entity/Shema/PrimaryKey.php */
