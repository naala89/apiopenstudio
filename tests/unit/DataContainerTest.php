<?php

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use Codeception\Test\Unit;

class DataContainerTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testDataContainerBool()
    {
        $container = new DataContainer(true);
        $this->assertIsBool($container->getData(), 'Boolean true is no longer a boolean.');
        $this->assertTrue($container->getData(), 'Boolean true is no longer a boolean.');
        $this->assertEquals('boolean', $container->getType(), 'Boolean true has the wrong data type.');

        $container = new DataContainer(true, 'boolean');
        $this->assertIsBool($container->getData(), 'Explicitly cast boolean true is no longer a boolean.');
        $this->assertTrue($container->getData(), 'Boolean true is no longer a boolean.');
        $this->assertEquals('boolean', $container->getType(), 'Boolean true has the wrong data type.');

        $container = new DataContainer(false);
        $this->assertIsBool($container->getData(), 'Boolean false is no longer a boolean.');
        $this->assertFalse($container->getData(), 'Boolean true is no longer a boolean.');
        $this->assertEquals('boolean', $container->getType(), 'Boolean true has the wrong data type.');

        $container = new DataContainer(false, 'boolean');
        $this->assertIsBool($container->getData(), 'Explicitly cast boolean false is no longer a boolean.');
        $this->assertFalse($container->getData(), 'Data not stored correctly.');
        $this->assertEquals('boolean', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer("true");
        $this->assertIsString($container->getData(), 'Text true is no longer a stored as a string.');
        $this->assertEquals('true', $container->getData(), 'Text true is no longer stored as text.');
        $this->assertEquals('text', $container->getType(), 'Text true has the wrong data type.');

        $container = new DataContainer("true", 'boolean');
        $this->assertIsBool($container->getData(), 'Explicitly cast text true is no longer a boolean.');
        $this->assertEquals(true, $container->getData(), 'Explicitly cast text true is no longer stored as boolean.');
        $this->assertEquals('boolean', $container->getType(), 'Explicitly cast text true has the wrong data type.');

        $container = new DataContainer("false");
        $this->assertIsString($container->getData(), 'Text false is no longer a stored as a string.');
        $this->assertEquals('false', $container->getData(), 'Text false is no longer stored as text.');
        $this->assertEquals('text', $container->getType(), 'Text false has the wrong data type.');

        $container = new DataContainer("false", 'boolean');
        $this->assertIsBool($container->getData(), 'Explicitly cast text false is no longer a boolean.');
        $this->assertEquals(false, $container->getData(), 'Explicitly cast text false is no longer stored as boolean.');
        $this->assertEquals('boolean', $container->getType(), 'Explicitly cast text false has the wrong data type.');

        $container = new DataContainer("yes");
        $this->assertIsString($container->getData(), 'Text yes is no longer a stored as a string.');
        $this->assertEquals('yes', $container->getData(), 'Text yes is no longer stored as text.');
        $this->assertEquals('text', $container->getType(), 'Text yes has the wrong data type.');

        $container = new DataContainer("yes", 'boolean');
        $this->assertIsBool($container->getData(), 'Explicitly cast text yes is no longer a stored as a boolean.');
        $this->assertEquals(true, $container->getData(), 'Explicitly cast text yes is no longer stored as boolean.');
        $this->assertEquals('boolean', $container->getType(), 'Explicitly cast text yes has the wrong data type.');

        $container = new DataContainer("no");
        $this->assertIsString($container->getData(), 'Text no is no longer a stored as a string.');
        $this->assertEquals('no', $container->getData(), 'Text no is no longer stored as text.');
        $this->assertEquals('text', $container->getType(), 'Text no has the wrong data type.');

        $container = new DataContainer("no", 'boolean');
        $this->assertIsBool($container->getData(), 'Explicitly cast text no is no longer a stored as a boolean.');
        $this->assertEquals(false, $container->getData(), 'Explicitly cast text no is no longer stored as boolean.');
        $this->assertEquals('boolean', $container->getType(), 'Explicitly cast text no has the wrong data type.');

        $container = new DataContainer(1);
        $this->assertIsInt($container->getData(), 'numerical 1 is no longer a stored as an integer.');
        $this->assertEquals(1, $container->getData(), 'numerical 1 is no longer stored as numerical 1.');
        $this->assertEquals('integer', $container->getType(), 'numerical 1 has the wrong data type.');

        $container = new DataContainer(1, 'boolean');
        $this->assertIsBool($container->getData(), 'Explicitly cast numerical 1 is no longer a stored as a boolean.');
        $this->assertEquals(true, $container->getData(), 'Explicitly cast numerical 1 is no longer stored as boolean.');
        $this->assertEquals('boolean', $container->getType(), 'Explicitly cast numerical 1 has the wrong data type.');

        $container = new DataContainer(0);
        $this->assertIsInt($container->getData(), 'numerical 0 is no longer a stored as an integer.');
        $this->assertEquals(0, $container->getData(), 'numerical 0 is no longer stored as numerical 0.');
        $this->assertEquals('integer', $container->getType(), 'numerical 0 has the wrong data type.');

        $container = new DataContainer(0, 'boolean');
        $this->assertIsBool($container->getData(), 'Explicitly cast numerical 0 is no longer a stored as a boolean.');
        $this->assertEquals(false, $container->getData(), 'Explicitly cast numerical 0 is no longer stored as boolean.');
        $this->assertEquals('boolean', $container->getType(), 'Explicitly cast numerical 0 has the wrong data type.');

        $this->expectException(ApiException::class);
        $container = new DataContainer(37, 'boolean');

        $this->expectException(ApiException::class);
        $container = new DataContainer("foobar", 'boolean');

        $this->expectException(ApiException::class);
        $container = new DataContainer(['foo' => 'bar'], 'boolean');

        $this->expectException(ApiException::class);
        $container = new DataContainer(json_encode(['foo' => 'bar'], true), 'boolean');

        $container = new DataContainer("true");
        $this->assertIsString($container->getData(), 'Text true is no longer a stored as a string.');
        $this->assertEquals('true', $container->getData(), 'Text true is no longer stored as text.');
        $this->assertEquals('text', $container->getType(), 'Text true has the wrong data type.');
        $container->setType('boolean');
        $this->assertIsBool($container->getData(), 'Explicitly cast text true is no longer a stored as a boolean.');
        $this->assertEquals(true, $container->getData(), 'Explicitly cast text true is no longer stored as boolean.');
        $this->assertEquals('boolean', $container->getType(), 'Explicitly cast text true has the wrong data type.');

        $container = new DataContainer("false");
        $this->assertIsString($container->getData(), 'Text false is no longer a stored as a string.');
        $this->assertEquals('false', $container->getData(), 'Text false is no longer stored as text.');
        $this->assertEquals('text', $container->getType(), 'Text false has the wrong data type.');
        $container->setType('boolean');
        $this->assertIsBool($container->getData(), 'Explicitly cast text false is no longer a stored as a boolean.');
        $this->assertEquals(false, $container->getData(), 'Explicitly cast text false is no longer stored as boolean.');
        $this->assertEquals('boolean', $container->getType(), 'Explicitly cast text false has the wrong data type.');
    }

    public function testDataContainerInt()
    {
        $this->expectException(ApiException::class);

        $container = new DataContainer(37);
        $this->assertIsInt($container->getData(), 'Number 37 is no longer a stored as an integer.');
        $this->assertEquals(37, $container->getData(), 'Number 37 is no longer stored as number 37.');
        $this->assertEquals('integer', $container->getType(), 'Number 37 is stored with the incorrect data type.');

        $container = new DataContainer(37, 'integer');
        $this->assertIsInt($container->getData(), 'Explicitly cast number 37 is no longer a stored as an integer.');
        $this->assertEquals(37, $container->getData(), 'Explicitly cast number 37 is no longer stored as number 37.');
        $this->assertEquals('integer', $container->getType(), 'Explicitly cast number 37 is stored with the incorrect data type.');

        $container = new DataContainer("37");
        $this->assertIsString($container->getData(), 'Text 37 is no longer a stored as an string.');
        $this->assertEquals("37", $container->getData(), 'Text 37 is no longer stored as text 37.');
        $this->assertEquals('text', $container->getType(), 'Text 37 is stored with the incorrect data type.');

        $container = new DataContainer("37", 'integer');
        $this->assertIsInt($container->getData(), 'Explicitly cast number 37 is no longer a stored as an integer.');
        $this->assertEquals(37, $container->getData(), 'Explicitly cast number 37 is no longer stored as number 37.');
        $this->assertEquals('integer', $container->getType(), 'Explicitly cast number 37 is stored with the incorrect data type.');

        $container = new DataContainer(0);
        $this->assertIsInt($container->getData(), 'Number 0 is no longer a stored as an integer.');
        $this->assertEquals(0, $container->getData(), 'Number 0 is no longer stored as number 37.');
        $this->assertEquals('integer', $container->getType(), 'Number 0 is stored with the incorrect data type.');

        $container = new DataContainer(0, 'integer');
        $this->assertIsInt($container->getData(), 'Explicitly cast number 0 is no longer a stored as an integer.');
        $this->assertEquals(0, $container->getData(), 'Explicitly cast number 0 is no longer stored as number 37.');
        $this->assertEquals('integer', $container->getType(), 'Explicitly cast number 0 is stored with the incorrect data type.');

        $container = new DataContainer(-653);
        $this->assertIsInt($container->getData(), 'Negative value is no longer a stored as an integer.');
        $this->assertEquals(-653, $container->getData(), 'Negative value is not stored correctly.');
        $this->assertEquals('integer', $container->getType(), 'Negative value has an incorrect data type stored.');

        $container = new DataContainer("0");
        $this->assertIsString($container->getData(), 'string of 0 is no longer a stored as an text.');
        $this->assertEquals("0", $container->getData(), 'string of 0 not stored correctly.');
        $this->assertEquals('text', $container->getType(), 'Incorrect data type stored for string of 0.');

        $container = new DataContainer("04000000");
        $this->assertIsString($container->getData(), 'string of 0 is no longer a stored as an text.');
        $this->assertEquals("04000000", $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('text', $container->getType(), 'Incorrect data type stored.');
        $container->setType('integer');
        $this->assertEquals(4000000, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('integer', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer("04000000", 'integer');
        $this->assertEquals(4000000, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('integer', $container->getType(), 'Incorrect data type stored.');

        $this->expectException(ApiException::class);
        $container = new DataContainer(37.76, 'integer');

        $this->expectException(ApiException::class);
        $container = new DataContainer("foobar", 'integer');

        $this->expectException(ApiException::class);
        $container = new DataContainer(['foo' => 'bar'], 'integer');

        $this->expectException(ApiException::class);
        $container = new DataContainer(json_encode(['foo' => 'bar'], true), 'integer');
    }

    public function testDataContainerFloat()
    {
        $container = new DataContainer(3.7);
        $this->assertIsFloat($container->getData(), 'float is no longer a stored as an float.');
        $this->assertEquals(3.7, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('float', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer(3.7, 'float');
        $this->assertIsFloat($container->getData(), 'explicitly cast float is no longer a stored as an float.');
        $this->assertEquals(3.7, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('float', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer('3.7');
        $this->assertIsString($container->getData(), 'string float is no longer a stored as a string.');
        $this->assertEquals('3.7', $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('text', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer('3.7', 'float');
        $this->assertEquals(3.7, $container->getData(), 'Data not stored correctly.');
        $this->assertIsFloat($container->getData(), 'Data not stored correctly.');
        $this->assertEquals('float', $container->getType(), 'Incorrect data type stored.');

        $this->expectException(ApiException::class);
        $container = new DataContainer(['3.7'], 'float');

        $this->expectException(ApiException::class);
        $container = new DataContainer('{"id":31,"name": "joe"}', 'float');
    }

    public function testDataContainerEmpty()
    {
        $container = new DataContainer('');
        $this->assertEquals('', $container->getData(), 'Data not stored correctly.');
        $this->assertEmpty($container->getData(), 'Data not stored correctly.');
        $this->assertEquals('empty', $container->getType(), 'Incorrect data type stored.');

        $container = new DataContainer(null);
        $this->assertEquals(null, $container->getData(), 'Data not stored correctly.');
        $this->assertEmpty($container->getData(), 'Data not stored correctly.');
        $this->assertEquals('empty', $container->getType(), 'Incorrect data type stored.');
    }

    public function testDataContainerArray()
    {
        $container = new DataContainer(['this', 'will', 'work']);
        $this->assertIsArray($container->getData(), 'Expected data to an array');
        $this->assertEquals(['this', 'will', 'work'], $container->getData(), 'Expected data to an array');
        $this->assertEquals('array', $container->getType(), 'Expected type to be "array"');

        $container = new DataContainer(['this', 'will', 'work'], 'array');
        $this->assertIsArray($container->getData(), 'Expected data to an array');
        $this->assertEquals(['this', 'will', 'work'], $container->getData(), 'Expected data to an array');
        $this->assertEquals('array', $container->getType(), 'Expected type to be "array"');

        $container = new DataContainer('["this","is","json"]');
        $this->assertNotEquals(["this","is","json"], $container->getData(), 'Expected data to not be cast to an array.');
        $this->assertEquals('["this","is","json"]', $container->getData(), 'Expected data to be a JSON string.');
        $this->assertEquals('json', $container->getType(), 'Expected data type to be "json".');
        $container->setType('array');
        $this->assertEquals(["this","is","json"], $container->getData(), 'Expected data to be cast to an array.');
        $this->assertNotEquals('["this","is","json"]', $container->getData(), 'Expected data to no longer be a JSON string.');
        $this->assertEquals('array', $container->getType(), 'Expected type to be "array"');

        $container = new DataContainer('["this","is",invalid:"json"]');
        $this->assertEquals('["this","is",invalid:"json"]', $container->getData(), 'Expected data to remain text string.');
        $this->assertEquals('text', $container->getType(), 'Expected data type to be text.');
        $container->setType('array');
        $this->assertNotEquals(["this","is",'invalid' => "json"], $container->getData(), 'Data not stored correctly.');
        $this->assertEquals(['["this","is",invalid:"json"]'], $container->getData(), 'Data not stored correctly.');
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

    public function testDataContainerText()
    {
        $container = new DataContainer('This will succeed!');
        $this->assertEquals('This will succeed!', $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('text', $container->getType(), 'Incorrect data type stored.');
    }

    public function testChangeToInvalid()
    {
        $this->expectException(ApiException::class);

        $container = new DataContainer('This will succeed!');
        $this->assertEquals('This will succeed!', $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('text', $container->getType(), 'Incorrect data type stored.');

        // These are acceptable if the dev wants to manipulate the DataContainer.

        $container->setType('boolean');
        $this->assertEquals('', $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('boolean', $container->getType(), 'Incorrect data type stored.');

        $container->setData(37);
        $this->assertEquals(37, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('boolean', $container->getType(), 'Incorrect data type stored.');

        $container->setType('integer');
        $this->assertEquals(37, $container->getData(), 'Data not stored correctly.');
        $this->assertEquals('integer', $container->getType(), 'Incorrect data type stored.');
    }
}
