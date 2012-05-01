<?php
class BV_Validate_UriTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->validator = new BV_Validate_Uri;
    }

    public function testBasicTest()
    {
        $this->assertTrue($this->validator->isValid('http://example.com')); 
        $this->assertTrue($this->validator->isValid('https://example.com')); 
        $this->assertFalse($this->validator->isValid('httpsa://example.com')); 
        $this->assertTrue($this->validator->isValid('http://binaryvibes.co.in')); 
        $this->assertTrue($this->validator->isValid('http://bizsense.binaryvibes.co.in')); 
        $this->assertFalse($this->validator->isValid('bizsense.binaryvibes.co.in')); 
    }
}
