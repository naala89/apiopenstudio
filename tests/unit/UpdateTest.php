<?php

use ApiOpenStudio\Cli\Update;
use Codeception\Test\Unit;

class UpdateTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;

    /**
     * @var ApiOpenStudio\Cli\Update
     */
    protected Update $update;

    protected function _before()
    {
        $this->update = new Update();
    }

    protected function _after()
    {
    }

    // Test sorting for major version.
    public function testSortFunctionMajor()
    {
        $result = $this->update->sortByVersion('1.0.0', '2.0.0');
        $this->assertEquals(-1, $result, 'Invalid major comparison for $a < $b');

        $result = $this->update->sortByVersion('1.1.0', '2.0.0');
        $this->assertEquals(-1, $result, 'Invalid major comparison for $a < $b with medium');

        $result = $this->update->sortByVersion('1.0.1', '2.0.0');
        $this->assertEquals(-1, $result, 'Invalid major comparison for $a < $b with minor');

        $result = $this->update->sortByVersion('1.1.1', '2.0.0');
        $this->assertEquals(-1, $result, 'Invalid major comparison for $a < $b with medium and minor');

        $result = $this->update->sortByVersion('2.0.0', '1.0.0');
        $this->assertEquals(1, $result, 'Invalid major comparison for $a > $b');

        $result = $this->update->sortByVersion('2.0.0', '1.1.0');
        $this->assertEquals(1, $result, 'Invalid major comparison for $a > $b with medium');

        $result = $this->update->sortByVersion('2.1.0', '1.3.0');
        $this->assertEquals(1, $result, 'Invalid major comparison for $a > $b with medium');

        $result = $this->update->sortByVersion('2.0.0', '1.0.1');
        $this->assertEquals(1, $result, 'Invalid major comparison for $a > $b with minor');

        $result = $this->update->sortByVersion('2.0.2', '1.0.3');
        $this->assertEquals(1, $result, 'Invalid major comparison for $a > $b with minor');

        $result = $this->update->sortByVersion('2.0.0', '1.1.1');
        $this->assertEquals(1, $result, 'Invalid major comparison for $a > $b medium and minor');

        $result = $this->update->sortByVersion('2.0.0', '2.0.0');
        $this->assertEquals(0, $result, 'Invalid major comparison for $a == $b');
    }

    // Test sorting for medium version.
    public function testSortFunctionMedium()
    {
        $result = $this->update->sortByVersion('1.1.0', '1.2.0');
        $this->assertEquals(-1, $result, 'Invalid medium comparison for $a < $b');

        $result = $this->update->sortByVersion('1.1.2', '1.2.0');
        $this->assertEquals(-1, $result, 'Invalid medium comparison for $a < $b with minor');

        $result = $this->update->sortByVersion('1.3.0', '1.2.0');
        $this->assertEquals(1, $result, 'Invalid medium comparison for $a > $b');

        $result = $this->update->sortByVersion('1.3.1', '1.2.5');
        $this->assertEquals(1, $result, 'Invalid medium comparison for $a > $b with minor');

        $result = $this->update->sortByVersion('2.1.0', '2.1.0');
        $this->assertEquals(0, $result, 'Invalid medium comparison for $a == $b');
    }

    // Test sorting for minor version.
    public function testSortFunctionMinor()
    {
        $result = $this->update->sortByVersion('1.2.1', '1.2.3');
        $this->assertEquals(-1, $result, 'Invalid minor comparison for $a < $b');

        $result = $this->update->sortByVersion('1.2.3', '1.2.1');
        $this->assertEquals(1, $result, 'Invalid minor comparison for $a > $b');

        $result = $this->update->sortByVersion('1.2.1', '1.2.1');
        $this->assertEquals(0, $result, 'Invalid minor comparison for $a == $b');
    }

    // Test sorting for RC version.
    public function testSortFunctionRc()
    {
        $result = $this->update->sortByVersion('1.2.1-RC', '1.2.1');
        $this->assertEquals(-1, $result, 'Invalid RC comparison for $a < $b');

        $result = $this->update->sortByVersion('1.2.1', '1.2.2-RC');
        $this->assertEquals(-1, $result, 'Invalid RC comparison for $a < $b');

        $result = $this->update->sortByVersion('1.2.2-RC', '1.2.2-alpha');
        $this->assertEquals(1, $result, 'Invalid RC comparison alpha');

        $result = $this->update->sortByVersion('1.2.2-RC', '1.2.2-beta');
        $this->assertEquals(1, $result, 'Invalid RC comparison beta');

        $result = $this->update->sortByVersion('1.2.1', '1.2.1-rc');
        $this->assertEquals(1, $result, 'Invalid minor comparison with RC for $a > $b');

        $result = $this->update->sortByVersion('1.2.1-RC', '1.2.1');
        $this->assertEquals(-1, $result, 'Invalid minor comparison with RC for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-RC', '1.2.2');
        $this->assertEquals(-1, $result, 'Invalid minor comparison with RC for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-RC1', '1.2.1-RC2');
        $this->assertEquals(-1, $result, 'Invalid minor comparison with RC for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-RC2', '1.2.1-RC1');
        $this->assertEquals(1, $result, 'Invalid minor comparison with RC for $a > $b');

        $result = $this->update->sortByVersion('1.2.1-RC2', '1.2.1-alpha');
        $this->assertEquals(1, $result, 'Invalid RC comparison with alpha for $a > $b');

        $result = $this->update->sortByVersion('1.2.1-RC2', '1.2.1-beta');
        $this->assertEquals(1, $result, 'Invalid RC comparison with beta for $a > $b');

        $result = $this->update->sortByVersion('1.2.1-RC2', '1.2.1-alpha1');
        $this->assertEquals(1, $result, 'Invalid RC comparison with alpha for $a > $b');

        $result = $this->update->sortByVersion('1.2.1-RC2', '1.2.1-beta2');
        $this->assertEquals(1, $result, 'Invalid RC comparison with beta for $a > $b');
    }

    // Test sorting for alpha version.
    public function testSortFunctionAlpha()
    {
        $result = $this->update->sortByVersion('1.2.1-alpha', '1.2.1-alpha');
        $this->assertEquals(0, $result, 'Invalid alpha comparison for $a == $b');

        $result = $this->update->sortByVersion('1.2.1-alpha1', '1.2.1-alpha1');
        $this->assertEquals(0, $result, 'Invalid alpha comparison for $a == $b');

        $result = $this->update->sortByVersion('1.2.1-alpha', '1.2.1');
        $this->assertEquals(-1, $result, 'Invalid alpha comparison against full minor release for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-alpha', '1.3.1');
        $this->assertEquals(-1, $result, 'Invalid alpha comparison against full minor release for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-alpha', '1.1.1');
        $this->assertEquals(1, $result, 'Invalid alpha comparison against full medium release for $a > $b');

        $result = $this->update->sortByVersion('1.2.1-alpha', '2.3.1');
        $this->assertEquals(-1, $result, 'Invalid alpha comparison against full major release for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-alpha', '1.2.1-alpha2');
        $this->assertEquals(-1, $result, 'Invalid alpha comparison for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-alpha3', '1.2.1-alpha2');
        $this->assertEquals(1, $result, 'Invalid alpha comparison for $a > $b');

        $result = $this->update->sortByVersion('1.2.1-alpha3', '1.2.1-beta');
        $this->assertEquals(-1, $result, 'Invalid alpha comparison for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-alpha', '1.2.1-beta');
        $this->assertEquals(-1, $result, 'Invalid alpha comparison for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-alpha', '1.2.1-beta34');
        $this->assertEquals(-1, $result, 'Invalid alpha comparison for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-alpha', '1.2.1-RC1');
        $this->assertEquals(-1, $result, 'Invalid alpha comparison for $a < $b');
    }

    // Test sorting for alpha version.
    public function testSortFunctionBeta()
    {
        $result = $this->update->sortByVersion('1.2.1-beta', '1.2.1-beta');
        $this->assertEquals(0, $result, 'Invalid beta comparison for $a == $b');

        $result = $this->update->sortByVersion('1.2.1-beta1', '1.2.1-beta1');
        $this->assertEquals(0, $result, 'Invalid beta comparison for $a == $b');

        $result = $this->update->sortByVersion('1.2.1-beta', '1.2.1');
        $this->assertEquals(-1, $result, 'Invalid beta comparison against full minor release for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-beta', '1.3.1');
        $this->assertEquals(-1, $result, 'Invalid beta comparison against full minor release for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-beta', '1.1.1');
        $this->assertEquals(1, $result, 'Invalid beta comparison against full medium release for $a > $b');

        $result = $this->update->sortByVersion('1.2.1-beta', '2.3.1');
        $this->assertEquals(-1, $result, 'Invalid beta comparison against full major release for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-beta', '1.2.1-beta2');
        $this->assertEquals(-1, $result, 'Invalid beta comparison for $a < $b');

        $result = $this->update->sortByVersion('1.2.1-beta3', '1.2.1-beta2');
        $this->assertEquals(1, $result, 'Invalid beta comparison for $a > $b');

        $result = $this->update->sortByVersion('1.2.1-beta3', '1.2.1-alpha');
        $this->assertEquals(1, $result, 'Invalid beta comparison for $a > $b');

        $result = $this->update->sortByVersion('1.2.1-beta', '1.2.1-alpha');
        $this->assertEquals(1, $result, 'Invalid beta comparison for $a > $b');

        $result = $this->update->sortByVersion('1.2.1-beta', '1.2.1-alpha34');
        $this->assertEquals(1, $result, 'Invalid alpha comparison for $a > $b');

        $result = $this->update->sortByVersion('1.2.1-beta', '1.2.1-RC1');
        $this->assertEquals(-1, $result, 'Invalid beta comparison for $a < $b');
    }
}
