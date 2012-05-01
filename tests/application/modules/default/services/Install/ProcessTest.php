<?php
class Core_Service_Install_ProcessTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->process = new Core_Service_Install_Process;
        parent::setUp();
    }

    public function testCanDoUnitTest()
    {
        $this->assertTrue(true); 
    }

    public function testBasic()
    {
        $cacheLength = strlen($this->process->getRandomCacheId());
        $this->assertEquals(10, $cacheLength);
    }

   
}
