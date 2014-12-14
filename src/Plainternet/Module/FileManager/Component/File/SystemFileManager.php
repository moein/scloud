<?php

namespace Plainternet\Module\FileManager\Component\File;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

class SystemFileManager
{
    protected $tempDirectory;
    
    public function __construct($tempDirectory)
    {
        $this->tempDirectory = $tempDirectory;
    }
    
    /**
     * Returns a the iterator for files matching the extensions.
     * If no extension is provided all the files are returned
     * 
     * @param string $directory
     * @param array $extensions Extensions expected to be returned
     * @return Finder
     */
    public function getFilesInDirectory($directory, array $extensions = array())
    {
        $finder = new Finder();

        $iterator = $finder
            ->files()
            ->depth(0)
            ->in($directory);
        foreach ($extensions as $extension) {
            $iterator->name('/\.' . $extension . '$/i');
        }
        
        return $iterator;
    }
    
    public function removeFile($path)
    {
        @unlink($path);
    }
    
    public function removeDirectory($directory)
    {
        rmdir($directory);
    }
    
    public function move($source, $destination)
    {
        $file = new File($source);
        $file->move($destination);
    }
    
    /**
     * Unzips a file into the given directory
     * 
     * @param string $file
     * @param string $destination
     * @throws FileAccessDeniedException
     */
    public function unzip($file)
    {
        $destination = $this->tempDirectory . DIRECTORY_SEPARATOR . uniqid('scloud_');
        $zip = new \ZipArchive;
        $res = $zip->open($file);
        if ($res === true) {
          $zip->extractTo($destination);
          $zip->close();
        } else {
          throw new Exception\FileAccessDeniedException($file);
        }
        
        return $destination;
    }
    
    public function createSystemDirectory($directory, $recursive = false)
    {
        @mkdir($directory, 0777, $recursive);
    }
}