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

    public function setUp()
    {
        $this->class = new ApibToJson(file_get_contents(TEST_STATICS . '/apib'));
        $this->reflection = new ReflectionClass('PHPDraft\Parse\ApibToJson');
    }

    public function tearDown()
    {
        unset($this->class);
        unset($this->reflection);
    }

    public function testSetupCorrectly()
    {
        $property = $this->reflection->getProperty('apib');
        $property->setAccessible(TRUE);
        $this->assertEquals(file_get_contents(TEST_STATICS . '/apib'), $property->getValue($this->class));
    }

    public function testPreRunStringIsEmpty()
    {
        $this->assertEmpty($this->class->__toString());
    }

    public function testParseToJSON()
    {
        $this->class->parseToJson();
        $this->assertJsonStringEqualsJsonFile(TEST_STATICS.'/json', $this->class->__toString());
    }

}
