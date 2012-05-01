<?php
class Core_Model_Newsletter_Message_QueueTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->queue = new Core_Model_Newsletter_Message_Queue;
        parent::setUp();
    }

    public function testCanDoUnitTest()
    {
        $this->assertTrue(true); 
    }

    public function testBasic()
    {
        $to = 'from+user=example.com@sample.binaryvibes.co.in';
        $email = 'user@example.com';
        $this->assertEquals($this->queue->getEmailFromString($to), $email);
    }

   
}
