<?php

/**
 * This file contains the JsonToHTMLTest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Parse\LegacyHtmlGenerator;
use ReflectionClass;

/**
 * Class JsonToHTMLTest
 * @covers \PHPDraft\Parse\LegacyHtmlGenerator
 */
class LegacyHtmlGeneratorTest extends LunrBaseTest
{
    /**
     * Test Class
     * @var LegacyHtmlGenerator
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
    public function setUp(): void
    {
        $data        = json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $this->class = new LegacyHtmlGenerator();
        $this->class->init($data);

        $this->reflection = new ReflectionClass('PHPDraft\Parse\LegacyHtmlGenerator');
        $this->mock_function('microtime', function () {
            return 'sometime';
        });

        $this->class->sorting = -1;
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        $this->unmock_function('microtime');
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
        $old = THIRD_PARTY_ALLOWED;
        $this->constant_redefine('THIRD_PARTY_ALLOWED', true);
        $this->expectOutputString(file_get_contents(TEST_STATICS . '/drafter/html/basic.html'));
        $this->class->get_html();
        $this->constant_redefine('THIRD_PARTY_ALLOWED', $old);
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    public function testGetHTMLInheritance(): void
    {
        $this->markTestSkipped('Not testing.');
        $old = THIRD_PARTY_ALLOWED;
        $this->constant_redefine('THIRD_PARTY_ALLOWED', true);
        $class = new LegacyHtmlGenerator();
        $class->init(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/inheritance.json')));
        $this->expectOutputString(file_get_contents(TEST_STATICS . '/drafter/html/inheritance.html'));
        $class->get_html();
        $this->constant_redefine('THIRD_PARTY_ALLOWED', $old);
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    public function testGetHTMLMaterial(): void
    {
        $old = THIRD_PARTY_ALLOWED;
        $this->constant_redefine('THIRD_PARTY_ALLOWED', true);
        $this->expectOutputString(file_get_contents(TEST_STATICS . '/drafter/html/material.html'));
        $this->class->get_html('material');
        $this->constant_redefine('THIRD_PARTY_ALLOWED', $old);
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    public function testGetHTMLAdvanced(): void
    {
        $return = $this->class->get_html('temp', 'img.jpg', 'test.css,index.css', 'index.js,test.js');
        $this->assertSame([['test.css', 'index.css']], $return->css);
        $this->assertSame([['index.js', 'test.js']], $return->js);
    }
}
