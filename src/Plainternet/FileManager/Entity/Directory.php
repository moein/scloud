<?php

namespace Plainternet\FileManager\Entity;

class Directory
{
    const ROOT_PATH = '.';
    
    /** @var Directory */
    protected $parent;
    
    protected $owner;
    
    /**
     * The relative path to the root directory. In case of root directory it's "."
     *
     * @var string
     */
    protected $path;
    
    /** @var string */
    protected $name;
    
    public function __construct($name, Directory $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        
        if (is_null($this->parent)) {
            $this->path = self::ROOT_PATH;
        } else {
            $this->path = $this->parent->getPath() . PATH_SEPARATOR . $this->name;
        }
    }
    
    public function getOwner()
    {
        return $this->owner;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getPath()
    {
        return $this->path;
    }
}

