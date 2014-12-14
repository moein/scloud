<?php

namespace Plainternet\Module\FileManager\Entity;

use Plainternet\Module\FileManager\Model\UserInterface;

class Directory
{
    const ROOT_PATH = '.';
    
    protected $id;
    
    /** @var Directory */
    protected $parent;
    
    /** @var File[] */
    protected $files = [];
    
    /** @var Directory[] */
    protected $children = [];
    
    /** @var UserInterface */
    protected $owner;
    
    /** @var \DateTime */
    protected $createdAt;
    
    /**
     * The relative path to the root directory. In case of root directory it's "."
     *
     * @var string
     */
    protected $path;
    
    /** @var string */
    protected $name;
    
    public function __construct($name, UserInterface $owner, Directory $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->owner = $owner;
        $this->createdAt = new \DateTime;
        
        if (is_null($this->parent)) {
            $this->path = self::ROOT_PATH;
        } else {
            $this->path = $this->parent->getPath() . DIRECTORY_SEPARATOR . $this->name;
            $parent->addChild($this);
        }
    }
    
    protected function addChild(Directory $child)
    {
        $this->children[] = $child;
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
    
    function getId()
    {
        return $this->id;
    }

    /**
     * 
     * @return Directory
     */
    function getParent()
    {
        return $this->parent;
    }

    /**
     * 
     * @return File[]
     */
    function getFiles()
    {
        return $this->files;
    }
    
    /**
     * 
     * @return Directory[]
     */
    function getChildren()
    {
        return $this->children;
    }

    /**
     * 
     * @return \Datetime
     */
    function getCreatedAt()
    {
        return $this->createdAt;
    }
}

