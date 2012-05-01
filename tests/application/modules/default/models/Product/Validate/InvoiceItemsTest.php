<?php
class Core_Model_Product_Validate_InvoiceItemsTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->validator = new Core_Model_Product_Validate_InvoiceItems;
        parent::setUp();
    }

    public function testCanDoUnitTest()
    {
        $this->assertTrue(true); 
    }

    public function testBasic()
    {
        $this->assertFalse($this->validator->isValid('wrong data'));
    }

    public function testItemValues()
    {
        $values = array(
            'product_id' => array (1,2,3),
            'item_description' => array('', '', ''),
            'tax_type_id' => array('', '', ''),
            'quantity' => array(20, 30, 40),
            'unit_price' => array('34.55', '344.44', '44.44')
        );
        $this->assertTrue($this->validator->isValid($values));
    }

    public function testInvalidSamples()
    {
        $invalids = array();

        $invalids[] = array(
            'service_item_id' => array (1,2,'james'),
            'quantity' => array(20, 30, 40),
            'unit_price' => array('34.55', '344.44', '44.44')
        );

        $invalids[] = array(
            'service_item_id' => array (1,2,'3'),
            'quantity' => array(20, 'four', 40),
            'unit_price' => array('34.55', '344.44', '44.44')
        );

        $invalids[] = array(
            'service_item_id' => array (1,2,'3'),
            'quantity' => array(20, 'four', 40),
            'unit_price' => array('34.55', '344.44', 'nan')
        );

        $invalids[] = array(
            'service_item_id' => array (1,2,''),
            'quantity' => array(20, '', 40),
            'unit_price' => array('', '344.44', 'nan')
        );


        foreach ($invalids as $invalid) {
            $this->assertFalse($this->validator->isValid($invalid));
        }
    }

}
