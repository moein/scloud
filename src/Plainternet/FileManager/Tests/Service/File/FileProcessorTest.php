<?php

namespace Plainternet\FileManager\Tests\Service\File;

use Prophecy;
use Plainternet\FileManager\Service\File\FileProcessor;
use Plainternet\FileManager\Service\File\Exception\ConflictiveHandlerException;

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
    
    public function testRegisterHandler()
    {
        $fileProcessor = $this->getFileProcessor();
        
        //Prophecy for handler
        $prophecy = $this->getProphecy();
        $prophecy->willImplement('Plainternet\FileManager\Service\File\FileHandlerInterface');
        
        $extensions = array('jpg', 'jpeg');
        $handler = $prophecy->reveal();
        $fileProcessor->registerHandler($extensions, $handler);
        $handlers = $fileProcessor->getHandlers();
        
        $this->assertEquals($extensions, array_keys($handlers), 'The indexes of the handlers are based on extensions');
        $this->assertInstanceOf('Plainternet\FileManager\Service\File\FileHandlerInterface', $handlers['jpg']);
        
        $fileProcessor->registerHandler(array('png'), $handler);
        $this->assertArrayHasKey('png', $fileProcessor->getHandlers());
        
        $exception = null;
        try {
            $fileProcessor->registerHandler(array('jpeg'), $handler);
        } catch (ConflictiveHandlerException $e) {
            $exception = $e;
        }
        
        $this->assertInstanceOf(
            'Plainternet\FileManager\Service\File\Exception\ConflictiveHandlerException',
            $e,
            'FileProcessor should throws an exception in case of registering a handler for an extension that is already registered!'
        );
    }
}