<?php

namespace Plainternet\Module\FileManager\Application\Repository;

use Plainternet\Module\FileManager\Repository\FileRepositoryInterface;
use Plainternet\Module\FileManager\Model\UserInterface;

class FileRepository extends EntityRepository implements FileRepositoryInterface
{
    public function getRootDirectory(UserInterface $user)
    {
             
    }

}

