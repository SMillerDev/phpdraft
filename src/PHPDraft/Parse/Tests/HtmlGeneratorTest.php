<?php

/**
 * This file contains the JsonToHTMLTest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Parse\HtmlGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;

/**
 * Class HtmlGeneratorTest
 */
#[CoversClass(HtmlGenerator::class)]
class HtmlGeneratorTest extends LunrBaseTestCase
{
    /**
     * Test Class
     * @var HtmlGenerator
     */
    protected HtmlGenerator $class;

    /**
     * Set up
     */
    #[RequiresPhpExtension('uopz')]
    public function setUp(): void
    {
        define('ID_STATIC', 'SOME_ID');
        $data        = json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $this->class = new HtmlGenerator();

        $this->baseSetUp($this->class);
        $this->class->init($data);

        $this->class->sorting = -1;
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        $this->undefineConstant('ID_STATIC');
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    #[RequiresPhpExtension('uopz')]
    public function testSetupCorrectly(): void
    {
        $json = json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $this->assertEquals($json, $this->getReflectionPropertyValue('object'));
    }

    /**
     * Tests if the constructor sets the property correctly
     */
    #[RequiresPhpExtension('uopz')]
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
     */
    #[RequiresPhpExtension('uopz')]
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
     */
    #[RequiresPhpExtension('uopz')]
    public function testGetHTMLAdvanced(): void
    {
        $this->class->build_html('material', 'img.jpg', 'test.css,index.css', 'index.js,test.js');

        $this->assertMatchesRegularExpression('/<link rel="stylesheet" href="(test|index)\.css">/', $this->class->__toString());
        $this->assertMatchesRegularExpression('/<script src="(test|index)\.js"><\/script>/', $this->class->__toString());
    }
}
