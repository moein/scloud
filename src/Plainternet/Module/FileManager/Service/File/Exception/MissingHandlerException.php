<?php

namespace Plainternet\Module\FileManager\Service\File\Exception;

class MissingHandlerException extends \Exception
{
    public function __construct($extension)
    {
        parent::__construct(sprintf('Called batch processing for a file with extension "%s" but there was no handler for it', $extension));
    }
}