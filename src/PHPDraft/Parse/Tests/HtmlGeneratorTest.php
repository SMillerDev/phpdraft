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
        $this->constant_undefine('ID_STATIC');
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Tests if the constructor sets the property correctly
     *
     * @requires ext-uopz
     */
    public function testSetupCorrectly(): void
    {
        $property = $this->reflection->getProperty('object');
        $property->setAccessible(true);
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')), $property->getValue($this->class));
    }

    /**
     * Tests if the constructor sets the property correctly
     * @requires ext-uopz
     */
    public function testGetHTML(): void
    {
        $this->class->build_html();
        if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
            $this->assertStringEqualsFile(TEST_STATICS . '/drafter/html/basic.html', $this->class->__toString());
        } else {
            $this->assertStringEqualsFile(TEST_STATICS . '/drafter/html/basic_old.html', $this->class->__toString());
        }
    }

    /**
     * Tests if the constructor sets the property correctly
     * @requires ext-uopz
     */
    public function testGetHTMLMaterial(): void
    {
        $this->class->build_html('material');
        if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
            $this->assertStringEqualsFile(TEST_STATICS . '/drafter/html/material.html', $this->class->__toString());
        } else {
            $this->assertStringEqualsFile(TEST_STATICS . '/drafter/html/material_old.html', $this->class->__toString());
        }
    }

    /**
     * Tests if the constructor sets the property correctly
     * @requires ext-uopz
     */
    public function testGetHTMLAdvanced(): void
    {
        $this->class->build_html('material', 'img.jpg', 'test.css,index.css', 'index.js,test.js');

        $this->assertMatchesRegularExpression('/<link rel="stylesheet" href="(test|index)\.css">/', $this->class->__toString());
        $this->assertMatchesRegularExpression('/<script src="(test|index)\.js"><\/script>/', $this->class->__toString());
    }
}
