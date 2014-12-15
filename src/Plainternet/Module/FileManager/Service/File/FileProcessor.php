<?php

namespace Plainternet\Module\FileManager\Service\File;

use Plainternet\Module\FileManager\Component\File\SystemFileManager;
use Plainternet\Module\FileManager\Model\UserInterface;
use Plainternet\Module\FileManager\Service\File\Exception\MissingHandlerException;

class FileProcessor
{
    /** @var SystemFileManager */
    protected $systemFileManager;
    
    /** @var FileManager */
    protected $fileManager;
    
    /** @var FileHandlerInterface[] The handlers used for batch processing */
    protected $handlers = array();
    
    /** @var bool */
    protected $callHandlers = true;

    /**
     * @param SystemFileManager $systemFileManager
     */
    public function setSystemFileManager($systemFileManager)
    {
        $this->systemFileManager = $systemFileManager;
    }
    
    /**
     * @param FileManager $fileManager
     */
    public function setFileManager($fileManager)
    {
        $this->fileManager = $fileManager;
    }
    
    public function areHandlersDisabled()
    {
        return $this->callHandlers === false;
    }
    
    public function disableHandlers()
    {
        $this->callHandlers = false;
    }

    /**
     * Process a file and creates the data in the database
     * 
     * @param \SplFileInfo $file The path to be processed
     * @param string $destination The directory to move the file to
     */
    public function process(\SplFileInfo $file, $destination, UserInterface $owner)
    {
        $directory = $this->fileManager->createDiretoryRecursively($destination, $owner);
        $this->fileManager->createFile($file->getPathname(), $directory);
    }
    
    /**
     * Unzips a compressed file and process each file inside
     * 
     * @param string $compressedFile
     */
    public function batchProcess($compressedFile, UserInterface $owner)
    {
        $tempDirectory = $this->systemFileManager->unzip($compressedFile);
        $extensions = array_keys($this->handlers);
        $files = $this->systemFileManager->getFilesInDirectory($tempDirectory, $extensions);

        foreach ($files as $file) {
            $destination = $this->handle($file);
            $this->process($file, $destination, $owner);
        }
        
        $this->systemFileManager->removeDirectory($tempDirectory);
        
        return count($files);
    }
    
    /**
     * Registers a handler
     * 
     * @param array $extensions An array of extensions that are handled by this handler
     * @param \Plainternet\Module\FileManager\Service\File\FileHandlerInterface $handler
     */
    public function registerHandler(array $extensions, FileHandlerInterface $handler)
    {
        if (count(array_intersect($extensions, array_keys($this->getHandlers())))) {
            throw new Exception\ConflictiveHandlerException('Can not register two handlers for an extension!');
        }
        
        $this->handlers = array_merge(
            $this->getHandlers(),
            array_fill_keys($extensions, $handler)
        );
    }
    
    public function getHandlers()
    {
        return $this->handlers;
    }
    
    /**
     * 
     * @param \SplFileInfo $file
     */
    protected function handle($file)
    {
        $extension = strtolower($file->getExtension());
        if (!isset($this->getHandlers()[$extension])) {
            throw new MissingHandlerException($extension);
        }
        return $this->getHandlers()[$extension]->handle($file);
    }
}