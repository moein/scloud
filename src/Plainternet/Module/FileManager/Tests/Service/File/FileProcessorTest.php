<?php

namespace Plainternet\Module\FileManager\Tests\Service\File;

use Prophecy;
use Plainternet\Module\FileManager\Service\File\FileProcessor;
use Plainternet\Module\FileManager\Service\File\Exception\ConflictiveHandlerException;

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
    
    protected function tearDown()
    {
        $this->prophet->checkPredictions();
    }
    
    protected function getProphecy()
    {
        return $this->prophet->prophesize();
    }
    
    protected function getSystemFileManager()
    {
        $prophecy = $this->getProphecy();
        $prophecy->willExtend('Plainternet\Module\FileManager\Component\File\SystemFileManager');
        
        return $prophecy;
    }
    
    protected function getUser()
    {
        $prophecy = $this->getProphecy();
        $prophecy->willImplement('Plainternet\Module\FileManager\Model\UserInterface');
        
        return $prophecy;
    }
    
    protected function getDirectory()
    {
        $prophecy = $this->getProphecy();
        $prophecy->willExtend('Plainternet\Module\FileManager\Entity\Directory');
        
        return $prophecy;
    }
    
    protected function getFileManager()
    {
        $prophecy = $this->getProphecy();
        $prophecy->willExtend('Plainternet\Module\FileManager\Service\File\FileManager');
        
        return $prophecy;
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
        
        $systemFileManager = $this->getSystemFileManager();
        $systemFileManager->move(self::IRRELEVANT_SOURCE_PATH, self::IRRELEVANT_DESTINATION_PATH);
        
        $fileManager = $this->getFileManager();

        $fileManager->createDiretoryRecursively(
            Prophecy\Argument::any(), 
            Prophecy\Argument::type('Plainternet\Module\FileManager\Model\UserInterface')
        )->willReturn($this->getDirectory()->reveal());
        $fileManager->createFile(
            Prophecy\Argument::any(), 
            Prophecy\Argument::type('Plainternet\Module\FileManager\Entity\Directory')
        )->willReturn(null);
        
        $fileProcessor = $this->getFileProcessor();
        $fileProcessor->setFileManager($fileManager->reveal());
        
        $fileProcessor->process($file, self::IRRELEVANT_DESTINATION_PATH, $this->getUser()->reveal());
    }
    
    public function testRegisterHandler()
    {
        $fileProcessor = $this->getFileProcessor();
        
        //Prophecy for handler
        $prophecy = $this->getProphecy();
        $prophecy->willImplement('Plainternet\Module\FileManager\Service\File\FileHandlerInterface');
        
        $extensions = array('jpg', 'jpeg');
        $handler = $prophecy->reveal();
        $fileProcessor->registerHandler($extensions, $handler);
        $handlers = $fileProcessor->getHandlers();
        
        $this->assertEquals($extensions, array_keys($handlers), 'The indexes of the handlers are based on extensions');
        $this->assertInstanceOf('Plainternet\Module\FileManager\Service\File\FileHandlerInterface', $handlers['jpg']);
        
        $fileProcessor->registerHandler(array('png'), $handler);
        $this->assertArrayHasKey('png', $fileProcessor->getHandlers());
        
        $exception = null;
        try {
            $fileProcessor->registerHandler(array('jpeg'), $handler);
        } catch (ConflictiveHandlerException $e) {
            $exception = $e;
        }
        
        $this->assertInstanceOf(
            'Plainternet\Module\FileManager\Service\File\Exception\ConflictiveHandlerException',
            $e,
            'FileProcessor should throws an exception in case of registering a handler for an extension that is already registered!'
        );
    }
}