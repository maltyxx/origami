<?php

namespace Origami\Entity\Pluggin;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
interface PlugginInterface
{
    /**
     * Initalisateur
     */
    public function initialize();
    
    /**
     * Transfère l'entité
     * @param \Origami\Entity\Manager\Origami\Entity\Core\EntityInterface $entity
     */
    public function setEntity(Origami\Entity\Core\EntityInterface $entity);
    
    /**
     * Retourne le nom du pluggin
     * @return string
     */
    public function getName();
    
}