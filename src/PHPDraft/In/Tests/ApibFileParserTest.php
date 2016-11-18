<?php
/**
 * This file contains the ApibFileParserTest.php
 *
 * @package PHPDraft\In
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\In\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\In\ApibFileParser;
use ReflectionClass;

/**
 * Class ApibFileParserTest
 * @covers PHPDraft\In\ApibFileParser
 */
class ApibFileParserTest extends BaseTest
{

    /**
     * Set up tests
     * @return void
     */
    public function setUp()
    {
        $this->class      = new ApibFileParser(__DIR__ . '/ApibFileParserTest.php');
        $this->reflection = new ReflectionClass('PHPDraft\In\ApibFileParser');
    }

    /**
     * Test if setup is successful
     * @return void
     */
    public function testSetup()
    {
        $property = $this->reflection->getProperty('location');
        $property->setAccessible(TRUE);
        $this->assertSame(__DIR__ . '/', $property->getValue($this->class));
    }

}