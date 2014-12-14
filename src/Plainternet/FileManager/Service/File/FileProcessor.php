<?php

namespace Plainternet\FileManager\Service\File;

class FileProcessor
{
    /** @var FileManager */
    protected $fileManager;
    
    /** @var FileHandlerInterface[] The handlers used for batch processing */
    protected $handlers = array();
    
    /** @var string */
    protected $rootDirectory;
    
    /** @var string */
    protected $tempDirectory;
    
    /** @var bool */
    protected $callHandlers = true;
    
    /**
     * @param string $tempDirectory The directory used to unzip the files
     */
    public function __construct($tempDirectory)
    {
        $this->tempDirectory = $tempDirectory;
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
     * Sets the root directory of the files(The root of destination directory)
     * 
     * @param string $directory
     */
    public function setRootDirectory($directory)
    {
        $this->rootDirectory = $directory;
    }
    
    /**
     * Process a file and creates the data in the database
     * 
     * @param \SplFileInfo $file The path to be processed
     * @param string $destination The directory to move the file to
     */
    public function process(\SplFileInfo $file, $destination)
    {
        $this->fileManager->move($file->getPathname(), $destination);
    }
    
    /**
     * Unzips a compressed file and process each file inside
     * 
     * @param string $compressedFile
     */
    public function batchProcess($compressedFile)
    {
        $tempDirectory = $this->createTempDirectory();
        $this->fileManager->unzip($compressedFile, $tempDirectory);
        $extensions = array_keys($this->handlers);
        $files = $this->fileManager->getFilesInDirectory($tempDirectory, $extensions);
        foreach ($files as $file) {
            $destination = $this->handle($file);
            $this->process($file, $destination);
        }
    }
    
    /**
     * Registers a handler
     * 
     * @param array $extensions An array of extensions that are handled by this handler
     * @param \Plainternet\FileManager\Service\File\FileHandlerInterface $handler
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
        return $this->getHandlers()[$file->getExtension()]->handle($file);
    }
    
    /**
     * @return string The newly created temporary directory
     */
    protected function createTempDirectory()
    {
        $directory = $this->tempDirectory . DIRECTORY_SEPARATOR . uniqid('scloud_');
        $this->fileManager->createDirectory($directory);
        
        return $directory;
    }
}