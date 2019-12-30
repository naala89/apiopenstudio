<?php

use Gaterdata\Core\Request;
use Gaterdata\Processor\VarBool;

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

    protected function _before()
    {
        $this->request = new Request();
    }

    protected function _after()
    {
    }

    public function testValReturnsContainerOrValue()
    {
        $meta = json_decode(json_encode([
            'function' => 'var_bool',
            'id' => 'var_bool literal true',
            'value' => true,
        ]));
        $varBool = new VarBool($meta, $this->request, '');
        $val = $varBool->val('value');
        $this->assertTrue(is_object($val), 'Return value is not class.');
        $this->assertTrue(get_class($val) == 'Gaterdata\Core\DataContainer', 'Return object is not DataContainer');
        $this->assertTrue($val->getData());
        $val = $varBool->val('value', true);
        $this->assertFalse(is_object($val), 'Return value is a class.');
        $this->assertTrue($val);
    }

    public function testValReturnsDefault()
    {
        $meta = json_decode(json_encode([
            'function' => 'var_bool',
            'id' => 'var_bool default',
            'value' => '',
        ]));
        $varBool = new VarBool($meta, $this->request, '');
        $val = $varBool->val('value', true);
        $this->assertTrue($val === false);

        $meta = json_decode(json_encode([
            'function' => 'var_bool',
            'id' => 'var_bool default',
            'value' => null,
        ]));
        $varBool = new VarBool($meta, $this->request, '');
        $val = $varBool->val('value', true);
        $this->assertTrue($val === false);
    }

    public function testInvalidValueNumeric()
    {
        $this->expectException("Exception");
        $this->expectExceptionCode(7);
        $this->expectExceptionMessage("invalid type (integer), only 'boolean' allowed");

        $meta = json_decode(json_encode([
            'function' => 'var_bool',
            'id' => 'var_bool literal true',
            'value' => 34,
        ]));
        $varBool = new VarBool($meta, $this->request, '');
        $val = $varBool->val('value', true);
    }

    public function testInvalidValueString()
    {
        $this->expectException("Exception");
        $this->expectExceptionCode(7);
        $this->expectExceptionMessage("invalid type (text), only 'boolean' allowed");

        $meta = json_decode(json_encode([
            'function' => 'var_bool',
            'id' => 'var_bool literal true',
            'value' => 'I will fail',
        ]));
        $varBool = new VarBool($meta, $this->request, '');
        $val = $varBool->val('value', true);
    }

    public function testInvalidValueArray()
    {
        $this->expectException("Exception");
        $this->expectExceptionCode(7);
        $this->expectExceptionMessage("invalid type (array), only 'boolean' allowed");

        $meta = json_decode(json_encode([
            'function' => 'var_bool',
            'id' => 'var_bool literal true',
            'value' => ['I will fail'],
        ]));
        $varBool = new VarBool($meta, $this->request, '');
        $val = $varBool->val('value', true);
    }

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
        $varBool = new VarBool($meta, $this->request, '');
        $val = $varBool->val('value', true);
    }
}
