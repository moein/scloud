<?php

namespace Plainternet\Module\FileManager\Entity;

class File
{
    public static $types = [
        'jpg'  => 'image',
        'jpeg' => 'image',
        'png'  => 'image',
        'pdf'  => 'pdf'
    ];
    
    protected $id;
    
    protected $name;
    
    /** @var Directory */
    protected $directory;
    
    public function __construct($name, Directory $directory)
    {
        $this->setName($name);
        $this->setDirectory($directory);
    }
    
    public function getExtension()
    {
        $nameParts = explode('.', $this->getName());
        
        return array_pop($nameParts);
    }
    
    function getName()
    {
        return $this->name;
    }

    function getDirectory()
    {
        return $this->directory;
    }

    function getType()
    {
        $extension = $this->getExtension();
        if (!isset(self::$types[$extension])) {
            return null;
        }
        
        return self::$types[$extension];
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function setDirectory(Directory $directory)
    {
        $this->directory = $directory;
    }

    function setType($type)
    {
        $this->type = $type;
    }


}