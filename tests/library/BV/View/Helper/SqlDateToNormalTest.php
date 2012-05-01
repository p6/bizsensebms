<?php
class BV_View_Helper_SqlDateToNormalTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->helper = new BV_View_Helper_SqlDateToNormal();
    }

    public function testBasicTest()
    {
        $testData = array(
            array('input' => '2009-11-12', 'output'=>'December 11, 2009'),
            array('input' => '1981-11-10', 'output'=>'October 11, 1981'),
        );
        foreach ($testData as $datum){
            $this->assertSame(
                    $datum['output'], 
                    $this->helper->sqlDateToNormal($datum['input'])
            );
        }
        
    }
}
