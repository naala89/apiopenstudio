<?php

use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Processor\VarBool;
use ApiOpenStudio\Core\ApiException;

class ValTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Request
     */
    protected $request;

    /**
     * {@inheritDoc}
     */
    protected function _before()
    {
        $this->request = new Request();
    }

    /**
     * {@inheritDoc}
     */
    protected function _after()
    {
    }

    /**
     * Test a processor returns a DataContainer.
     *
     * @throws ApiException
     */
    public function testValReturnsContainerOrValue()
    {
        $meta = json_decode(json_encode([
            'function' => 'var_bool',
            'id' => 'var_bool literal true',
            'value' => true,
        ]));
        $varBool = new VarBool($meta, $this->request, null, null);
        $val = $varBool->val('value');
        $this->assertIsObject($val, 'Return value is not class.');
        $this->assertTrue(
            get_class($val) == 'ApiOpenStudio\Core\DataContainer',
            'Return object is not DataContainer'
        );
        $this->assertTrue($val->getData());
        $val = $varBool->val('value', true);
        $this->assertFalse(is_object($val), 'Return value is a class.');
        $this->assertTrue($val);
    }

    /**
     * Test a container can return the default value.
     *
     * @throws ApiException
     */
    public function testValReturnsDefault()
    {
        $meta = json_decode(json_encode([
            'function' => 'var_bool',
            'id' => 'var_bool default',
            'value' => '',
        ]));
        $varBool = new VarBool($meta, $this->request,null, null);
        $val = $varBool->val('value', true);
        $this->assertTrue($val === false);

        $meta = json_decode(json_encode([
            'function' => 'var_bool',
            'id' => 'var_bool default',
            'value' => null,
        ]));
        $varBool = new VarBool($meta, $this->request, null, null);
        $val = $varBool->val('value', true);
        $this->assertTrue($val === false);
    }

//    /**
//     * Test an integer is not accepted for boolean type.
//     *
//     * @throws ApiException
//     * @TODO
//     */
//    public function testInvalidValueNumeric()
//    {
//        $this->expectException("Exception");
//        $this->expectExceptionCode(6);
//        $this->expectExceptionMessage("invalid type (integer), only 'boolean', 'integer', 'text' allowed in input 'value'");
//
//        $meta = json_decode(json_encode([
//            'function' => 'var_bool',
//            'id' => 'var_bool literal integer',
//            'value' => 'hi',
//        ]));
//        $varBool = new VarBool($meta, $this->request, null, null);
//        $val = $varBool->val('value', true);
//    }

//    /**
//     * Test a string is not allowed as boolean.
//     *
//     * @throws ApiException
//     * @TODO
//     */
//    public function testInvalidValueString()
//    {
//        $this->expectException("ApiException");
//        $this->expectExceptionCode(7);
//        $this->expectExceptionMessage("invalid type (text), only 'boolean' allowed");
//
//        $meta = json_decode(json_encode([
//            'function' => 'var_bool',
//            'id' => 'var_bool literal true',
//            'value' => 'I will fail',
//        ]));
//        $varBool = new VarBool($meta, $this->request, null, null);
//        $val = $varBool->val('value', true);
//    }

    /**
     * Test array is not accepted if it is not in the allowed types.
     *
     * @throws ApiException
     */
    public function testInvalidValueArray()
    {
        $this->expectException("Exception");
        $this->expectExceptionCode(6);
        $this->expectExceptionMessage(
            "invalid type (array), only 'boolean', 'integer', 'text' allowed in input 'value'"
        );

        $meta = json_decode(json_encode([
            'function' => 'var_bool',
            'id' => 'var_bool literal true',
            'value' => ['I will fail'],
        ]));
        $varBool = new VarBool($meta, $this->request, null, null);
        $val = $varBool->val('value', true);
    }

    /**
     * Test for an invalid number of inputs.
     *
     * @throws ApiException
     */
    public function testInvalidNumberInputs()
    {
        $this->expectException("Exception");
        $this->expectExceptionCode(7);
        $this->expectExceptionMessage("invalid number of inputs (2) in value, requires 1 - 1");

        $meta = json_decode(json_encode([
            'function' => 'var_bool',
            'id' => 'var_bool literal true',
            'value' => [
                'I will fail',
                true,
            ]
        ]));
        $varBool = new VarBool($meta, $this->request, null, null);
        $val = $varBool->val('value', true);
    }
}
