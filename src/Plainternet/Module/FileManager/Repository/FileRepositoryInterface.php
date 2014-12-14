<?php

namespace Plainternet\Module\FileManager\Repository;

use Plainternet\Module\FileManager\Model\UserInterface;

interface FileRepositoryInterface
{
    /**
     * Returns the root directory of the user
     * 
     * @param UserInterface $user
     */
    public function getRootDirectory(UserInterface $user);
    
    /**
     * Saves the given file or directory in the database
     * 
     * @param Directory|File $object
     */
    public function save($object);
}