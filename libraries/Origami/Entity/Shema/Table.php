<?php

namespace Origami\Entity\Shema;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Table
{
    /**
     * Nom de la table
     * @var string $name
     */
    private $name;

    /**
     * Contructeur
     * @param \Origami\Entity\Manager\Config $config
     */
    public function __construct(\Origami\Entity\Manager\Config &$config)
    {
        $this->setName($config);
    }

    /**
     * Retourne le nom de la table
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Modifie le nom de la table
     * @param \Origami\Entity\Manager\Config $config
     */
    public function setName(\Origami\Entity\Manager\Config &$config)
    {
        $this->name = $config->getTable();
    }

}

/* End of file Orm_table.php */
/* Location: ./libraries/Origami/Entity/Shema/Table.php */
