<?php

namespace Plainternet\FileManager\Service\File\Exception;

class FileAccessDeniedException extends \Exception
{
    public function __construct($file)
    {
        parent::__construct(sprintf('Could not access "%s"', $file));
    }
}