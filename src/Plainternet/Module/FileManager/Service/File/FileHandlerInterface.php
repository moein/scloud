<?php
namespace Plainternet\Module\FileManager\Service\File;

interface FileHandlerInterface
{
    /**
     * Handles the file and returns the relative directory that the file should be saved in
     * 
     * @param \SplFileInfo $file
     */
    public function handle(\SplFileInfo $file);
}
