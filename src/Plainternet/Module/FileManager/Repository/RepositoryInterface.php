<?php

namespace Plainternet\Module\FileManager\Repository;

interface RepositoryInterface
{
    /**
     * Saves the given file or directory in the database
     * 
     * @param Directory|File $object
     */
    public function save($object);
}