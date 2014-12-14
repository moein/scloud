<?php

namespace Plainternet\Module\FileManager\Application\Repository;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;

class EntityRepository extends BaseEntityRepository
{
    public function save($entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush($entity);
    }
}