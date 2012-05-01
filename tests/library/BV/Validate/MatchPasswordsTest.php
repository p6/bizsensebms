<?php
class BV_Validate_MatchPasswordsTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->validator = new BV_Validate_MatchPasswords;
    }

    public function testBasicTest()
    {
        $context = array('password'=>'password');
        $this->assertTrue($this->validator->isValid('password', $context)); 
        $this->assertFalse($this->validator->isValid('notmatchingpassword', $context)); 
    }
}
