<?php
/**
 * This file contains the JsonToHTMLTest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\Parse\JsonToHTML;
use ReflectionClass;

/**
 * Class JsonToHTMLTest
 * @covers PHPDraft\Parse\JsonToHTML
 */
class JsonToHTMLTest extends BaseTest
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

    /**
     * Set up
     */
    public function setUp()
    {
        $this->class      = new JsonToHTML(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')));
        $this->reflection = new ReflectionClass('PHPDraft\Parse\JsonToHTML');
        $this->mock_function('microtime', 'sometime');
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        $this->unmock_function('microtime');
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    public function testSetupCorrectly()
    {
        $property = $this->reflection->getProperty('object');
        $property->setAccessible(TRUE);
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')), $property->getValue($this->class));
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    public function testGetHTML()
    {
        $old = THIRD_PARTY_ALLOWED;
        $this->redefine('THIRD_PARTY_ALLOWED', TRUE);
        $this->expectOutputString(file_get_contents(TEST_STATICS . '/drafter/html/basic.html'));
        $this->class->get_html();
        $this->redefine('THIRD_PARTY_ALLOWED', $old);
    }
//
//    /**
//     * Tests if the constructor sets the property correctly
//     */
//    public function testGetHTMLInheritance()
//    {
//        $old = THIRD_PARTY_ALLOWED;
//        $this->redefine('THIRD_PARTY_ALLOWED', TRUE);
//        $class = new JsonToHTML(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/inheritance.json')));
//        $this->expectOutputString(file_get_contents(TEST_STATICS . '/drafter/html/inheritance.html'));
//        $class->get_html();
//        $this->redefine('THIRD_PARTY_ALLOWED', $old);
//    }

    /**
     * Tests if the constructor sets the property correctly
     */
    public function testGetHTMLMaterial()
    {
        $old = THIRD_PARTY_ALLOWED;
        $this->redefine('THIRD_PARTY_ALLOWED', TRUE);
        $this->expectOutputString(file_get_contents(TEST_STATICS . '/drafter/html/material.html'));
        $this->class->get_html('material');
        $this->redefine('THIRD_PARTY_ALLOWED', $old);
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    public function testToString()
    {
        $this->assertNotEmpty($this->class->__toString());
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    public function testGetHTMLAdvanced()
    {
        $return = $this->class->get_html('temp', 'img.jpg', 'test.css,index.css', 'index.js,test.js');
        $this->assertSame([['test.css', 'index.css']], $return->css);
        $this->assertSame([['index.js', 'test.js']], $return->js);
    }

}
