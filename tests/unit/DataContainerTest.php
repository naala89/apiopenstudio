<?php

use Gaterdata\Core\DataContainer;

class DataContainerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testDataContainerBool()
    {
        $container = new DataContainer(true);
        $this->assertTrue($container->getData(), 'Data not stored correctly.');
        $this->assertEquals('boolean', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer(true, 'boolean');
        $this->assertTrue($container->getData(), 'Data not stored correctly.');
        $this->assertEquals('boolean', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer(false);
        $this->assertFalse($container->getData(), 'Data not stored correctly.');
        $this->assertEquals('boolean', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer(false, 'boolean');
        $this->assertFalse($container->getData(), 'Data not stored correctly.');
        $this->assertEquals('boolean', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer(0, 'boolean');
        $this->assertEquals(0, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('boolean', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer("true");
        $this->assertEquals("true", $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('boolean', $container->getType(), 'Incorrect data type stored.');
    }

    public function testDataContainerInt()
    {
        $container = new DataContainer(37);
        $this->assertEquals(37, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('integer', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer("37");
        $this->assertEquals(37, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('integer', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer(0);
        $this->assertEquals(0, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('integer', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer(-653);
        $this->assertEquals(-653, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('integer', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer("0");
        $this->assertEquals(0, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('integer', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer("04000000");
        $this->assertEquals("04000000", $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('integer', $container->getType(), 'Incorrect data type stored.');
    }

    public function testDataContainerFloat()
    {
        $container = new DataContainer(3.7);
        $this->assertEquals(3.7, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('float', $container->getType(), 'Incorrect data type stored.');
        $container = new DataContainer("3.7");
        $this->assertEquals(3.7, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('float', $container->getType(), 'Incorrect data type stored.');
    }

    public function testDataContainerEmpty()
    {
        $container = new DataContainer('');
        $this->assertEquals('', $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('empty', $container->getType(), 'Incorrect data type stored.');
    }

    public function testDataContainerArray()
    {

        $container = new DataContainer(['this', 'will', 'fail']);
        $this->assertEquals(['this', 'will', 'fail'], $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('array', $container->getType(), 'Incorrect data type stored.');
    }

    public function testDataContainerJson()
    {
        $container = new DataContainer('{"this": "will", "fail": false}');
        $this->assertEquals('{"this": "will", "fail": false}', $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('json', $container->getType(), 'Incorrect data type stored.');
    }

    public function testDataContainerHtml()
    {
        $html = '<!DOCTYPE html>';
        $html .= '<html xmlns="http://www.w3.org/1999/xhtml" lang="en">';
        $html .= '  <head>';
        $html .= '    <title>test html</title>';
        $html .= '    <link rel="stylesheet" href="chrome-search://local-ntp/animations.css" />';
        $html .= '    <meta charset="utf-8" />';
        $html .= '    <meta name="referrer" content="strict-origin" />';
        $html .= '  </head>';
        $html .= '  <body>';
        $html .= '    <div id="custom-bg"></div>';
        $html .= '    <div id="custom-bg-preview"></div>';
        $html .= '    <div id="one-google"></div>';
        $html .= '  </body>';
        $html .= '</html>';
        $container = new DataContainer($html);
        $this->assertEquals($html, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('html', $container->getType(), 'Incorrect data type stored.');
    }

    public function testDataContainerBoolXml()
    {
        $xml = '<xml xmlns="http://www.w3.org/1999/xhtml" lang="en">';
        $xml .= '  <head>';
        $xml .= '    <title>test html</title>';
        $xml .= '    <link rel="stylesheet" href="chrome-search://local-ntp/animations.css" />';
        $xml .= '    <meta charset="utf-8" />';
        $xml .= '    <meta name="referrer" content="strict-origin" />';
        $xml .= '  </head>';
        $xml .= '  <body>';
        $xml .= '    <div id="custom-bg"></div>';
        $xml .= '    <div id="custom-bg-preview"></div>';
        $xml .= '    <div id="one-google"></div>';
        $xml .= '  </body>';
        $xml .= '</xml>';
        $container = new DataContainer($xml);
        $this->assertEquals($xml, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('xml', $container->getType(), 'Incorrect data type stored.');
    }

    public function testDataContainerString()
    {
        $container = new DataContainer('This will succeed!');
        $this->assertEquals('This will succeed!', $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('text', $container->getType(), 'Incorrect data type stored.');
    }

    public function testChangeToInvalid()
    {
        $container = new DataContainer('This will succeed!');
        $this->assertEquals('This will succeed!', $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('text', $container->getType(), 'Incorrect data type stored.');

        // These are acceptable if the dev wants to manipulate the the DataContainer.

        $container->setType('boolean');
        $this->assertEquals('This will succeed!', $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('boolean', $container->getType(), 'Incorrect data type stored.');

        $container->setData(37);
        $this->assertEquals(37, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('boolean', $container->getType(), 'Incorrect data type stored.');

        $container->setType('integer');
        $this->assertEquals(37, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('integer', $container->getType(), 'Incorrect data type stored.');
    }
}
