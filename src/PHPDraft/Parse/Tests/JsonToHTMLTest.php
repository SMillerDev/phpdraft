<?php
/**
 * This file contains the JsonToHTMLTest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use PHPDraft\Parse\JsonToHTML;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class JsonToHTMLTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Class
     * @var JsonToHTML
     */
    protected $class;

    /**
     * Test reflection
     * @var ReflectionClass
     */
    protected $reflection;

    public function setUp()
    {
        $this->class = new JsonToHTML(file_get_contents(TEST_STATICS . '/json'));
        $this->reflection = new ReflectionClass('PHPDraft\Parse\JsonToHTML');
    }

    public function tearDown()
    {
        unset($this->class);
        unset($this->reflection);
    }

    public function testSetupCorrectly()
    {
        $property = $this->reflection->getProperty('object');
        $property->setAccessible(TRUE);
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/json')), $property->getValue($this->class));
    }

    public function testParseToHTML()
    {
        $this->expectOutputString(file_get_contents(TEST_STATICS.'/html'));
        $this->class->get_html();
    }

}
