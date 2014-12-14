<?php

namespace Plainternet\Module\FileManager\Model;

interface UserInterface
{
    public function getId();
    
    /**
     * Returns the root directory of the user
     * 
     * @return Directory
     */
    public function getRootDirectory();
}