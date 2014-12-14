<?php

namespace Plainternet\Module\FileManager\Service\File;

use Plainternet\Module\FileManager\Component\File\SystemFileManager;
use Plainternet\Module\FileManager\Model\UserInterface;
use Plainternet\Module\FileManager\Entity\Directory;
use Plainternet\Module\FileManager\Repository\FileRepositoryInterface;
use Plainternet\Module\FileManager\Entity\File;
use Plainternet\Module\FileManager\Application\File\File as SystemFile;

class FileManager
{
    /** @var FileRepositoryInterface */
    protected $repository;
    
    /**
     * The path to the directories that all the files are stored
     *
     * @var string
     */
    protected $usersFilesDirectory;
    
    /** @var SystemFileManager */
    protected $systemFileManager;
    
    public function setRepository(FileRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    public function setUsersFilesDirectory($userFilesDirectory)
    {
        $this->usersFilesDirectory = $userFilesDirectory;
    }
    
    public function getUserFilesDirectory()
    {
        return $this->usersFilesDirectory;
    }
    
    public function setSystemFileManager(SystemFileManager $systemFileManager)
    {
        $this->systemFileManager = $systemFileManager;
    }
    
    public function createFile($source, Directory $directory)
    {
        $systemFile = new SystemFile($source);
        $file = new File($systemFile->getFilename(), $directory);
        $this->repository->save($file);
        
        $systemFile->move($this->getUserFilesDirectory() . DIRECTORY_SEPARATOR . $directory->getPath());
    }
    
    public function createDiretoryRecursively($directory, UserInterface $owner)
    {
        $directoryParts = array_filter(explode('/' , $directory));
        $parentDirectory = $owner->getRootDirectory();
        
        foreach ($directoryParts as $directoryPart) {
            $foundChildDirectory = null;
            foreach ($parentDirectory->getChildren() as $childDirectory) {
                if ($directoryPart === $childDirectory->getName()) {
                    $foundChildDirectory = $childDirectory;
                    break;
                }
            }
            
            if (is_null($foundChildDirectory)) {
                $parentDirectory = $this->createDirectory($directoryPart, $owner, $parentDirectory);
            } else {
                $parentDirectory = $foundChildDirectory;
            }
        }
        
        return $parentDirectory;
    }
    
    public function createDirectory($name, UserInterface $owner, Directory $parentDirectory = null)
    {
        $directory = new Directory($name, $owner, $parentDirectory);
        $this->repository->save($directory);
        
        $fullPath = $this->getUserFilesDirectory() . DIRECTORY_SEPARATOR . $directory->getPath();
        $this->systemFileManager->createSystemDirectory($fullPath);
        
        return $directory;
    }
}