<?php

/**
 * This file contains the JsonToHTMLTest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Parse\HtmlGenerator;
use ReflectionClass;

/**
 * Class JsonToHTMLTest
 * @covers \PHPDraft\Parse\HtmlGenerator
 */
class HtmlGeneratorTest extends LunrBaseTest
{
    /**
     * Test Class
     * @var HtmlGenerator
     */
    protected $class;

    /**
     * Test reflection
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * Set up
     * @requires ext-uopz
     */
    public function setUp(): void
    {
        define('ID_STATIC', 'SOME_ID');
        $data             = json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $this->class      = new HtmlGenerator();
        $this->reflection = new ReflectionClass('PHPDraft\Parse\HtmlGenerator');
        $this->class->init($data);

        $this->class->sorting = -1;
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        uopz_undefine('ID_STATIC');
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    public function testSetupCorrectly(): void
    {
        $property = $this->reflection->getProperty('object');
        $property->setAccessible(true);
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')), $property->getValue($this->class));
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    public function testGetHTML(): void
    {
        $this->class->build_html();
        $this->assertStringEqualsFile(TEST_STATICS . '/drafter/html/basic.html', $this->class->__toString());
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    public function testGetHTMLMaterial(): void
    {
        $this->class->build_html('material');
        $this->assertStringEqualsFile(TEST_STATICS . '/drafter/html/material.html', $this->class->__toString());
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    public function testGetHTMLAdvanced(): void
    {
        $this->class->build_html('temp', 'img.jpg', 'test.css,index.css', 'index.js,test.js');

        $this->assertMatchesRegularExpression('/<link rel="stylesheet" href="(test|index)\.css">/', $this->class->__toString());
        $this->assertMatchesRegularExpression('/<script src="(test|index)\.js"><\/script>/', $this->class->__toString());
    }
}
