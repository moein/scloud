<?php

namespace Plainternet\FileManager\Tests\Service\File;

use Prophecy;
use Plainternet\FileManager\Service\File\FileProcessor;

class FileProcessorTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_TEMP_PATH = '/tmp';
    const IRRELEVANT_SOURCE_PATH = 'srouce';
    const IRRELEVANT_DESTINATION_PATH = 'destination';
    
    /** @var Prophet */
    protected $prophet;
    
    protected function setUp()
    {
        $this->prophet = new Prophecy\Prophet;
    }
    
    protected function getProphecy()
    {
        return $this->prophet->prophesize();
    }
    
    protected function getFileManager()
    {
        $prophecy = $this->getProphecy();
        $prophecy->willExtend('Plainternet\FileManager\Service\File\FileManager');
        $prophecy->move(self::IRRELEVANT_SOURCE_PATH, self::IRRELEVANT_DESTINATION_PATH);
        
        return $prophecy->reveal();
    }
    
    /**
     * @return FileProcessor
     */
    protected function getFileProcessor()
    {
        return new FileProcessor(self::IRRELEVANT_TEMP_PATH);
    }
    
    
    
    public function testDisableHandler()
    {
        $fileProcessor = $this->getFileProcessor();
        
        $this->assertFalse($fileProcessor->areHandlersDisabled());
        $fileProcessor->disableHandlers();
        $this->assertTrue($fileProcessor->areHandlersDisabled());
    }
    
    public function testProcess()
    {
        $prophecy = $this->getProphecy();
        $prophecy->willExtend('\SplFileInfo');
        $file = $prophecy->reveal();
        $prophecy->getPathname()->willReturn(self::IRRELEVANT_SOURCE_PATH);
        
        $fileManager = $this->getFileManager();
        
        $fileProcessor = $this->getFileProcessor();
        $fileProcessor->setFileManager($fileManager);
        
        $fileProcessor->process($file, self::IRRELEVANT_DESTINATION_PATH);
    }
}