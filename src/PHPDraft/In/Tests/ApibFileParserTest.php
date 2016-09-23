<?php
/**
 * This file contains the ApibFileParserTest.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\In\Tests;


use PHPDraft\Core\TestBase;
use PHPDraft\In\ApibFileParser;
use ReflectionClass;

class ApibFileParserTest extends TestBase
{
    public function setUp()
    {
        $this->class      = new ApibFileParser(__DIR__.'/ApibFileParserTest.php');
        $this->reflection = new ReflectionClass('PHPDraft\In\ApibFileParser');
    }

    public function tearDown()
    {
        unset($this->class);
        unset($this->reflection);
    }

    public function testSetup()
    {
        $property = $this->reflection->getProperty('location');
        $property->setAccessible(TRUE);
        $this->assertSame(__DIR__.'/', $property->getValue($this->class));
    }


}