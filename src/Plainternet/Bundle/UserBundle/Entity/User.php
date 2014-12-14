<?php

namespace Plainternet\Bundle\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Plainternet\Module\FileManager\Model\UserInterface;

class User extends BaseUser implements UserInterface
{
    protected $rootDirectory;
    
    public function __construct()
    {
        parent::__construct();
    }

    public function getRootDirectory()
    {
        return $this->rootDirectory;
    }
}