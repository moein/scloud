<?php

namespace Plainternet\Module\FileManager\Service\FileHandler;

use Plainternet\Module\FileManager\Service\File\FileHandlerInterface;

class ImageHandler implements FileHandlerInterface
{
    public function handle(\SplFileInfo $file)
    {
        $datetime = new \DateTime;
        $datetime->setTimestamp($file->getMTime());
        
        return 'by-date' . DIRECTORY_SEPARATOR . str_replace('-', DIRECTORY_SEPARATOR, $datetime->format('Y-m-d'));
    }

}