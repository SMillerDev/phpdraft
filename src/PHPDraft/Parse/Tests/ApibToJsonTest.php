<?php
/**
 * This file contains the ApibToJsonTest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use PHPDraft\Parse\ApibToJson;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

/**
 * Class ApibToJsonTest
 * @covers PHPDraft\Parse\ApibToJson
 */
class ApibToJsonTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Class
     * @var ApibToJson
     */
    protected $class;

    /**
     * Test reflection
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->class = new ApibToJson(file_get_contents(TEST_STATICS . '/apib'));
        $this->reflection = new ReflectionClass('PHPDraft\Parse\ApibToJson');
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly()
    {
        $property = $this->reflection->getProperty('apib');
        $property->setAccessible(TRUE);
        $this->assertEquals(file_get_contents(TEST_STATICS . '/apib'), $property->getValue($this->class));
    }

    /**
     * Check if the JSON is empty before parsing
     */
    public function testPreRunStringIsEmpty()
    {
        $this->assertEmpty($this->class->__toString());
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     */
    public function testParseToJSON()
    {
        $this->class->parseToJson();
        $this->assertJsonStringEqualsJsonFile(TEST_STATICS.'/json', $this->class->__toString());
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     */
    public function testParseToJSONWithErrors()
    {
        $property = $this->reflection->getProperty('apib');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, file_get_contents(TEST_STATICS . '/apib_errors'));
        $this->class->parseToJson();
        $this->assertJsonStringEqualsJsonFile(TEST_STATICS.'/json_errors', $this->class->__toString());
    }
}
