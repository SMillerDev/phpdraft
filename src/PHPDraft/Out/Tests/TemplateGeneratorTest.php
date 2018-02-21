<?php
/**
 * This file contains the TemplateGeneratorTest.php
 *
 * @package PHPDraft\Out
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\Out\TemplateGenerator;

/**
 * Class TemplateGeneratorTest
 * @covers \PHPDraft\Out\TemplateGenerator
 */
class TemplateGeneratorTest extends BaseTest
{

    /**
     * Set up tests
     * @return void
     */
    public function setUp()
    {
        $this->class      = new TemplateGenerator('default', 'none');
        $this->reflection = new \ReflectionClass('PHPDraft\Out\TemplateGenerator');
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly()
    {
        $property = $this->reflection->getProperty('template');
        $property->setAccessible(TRUE);
        $this->assertSame('default', $property->getValue($this->class));
        $property = $this->reflection->getProperty('image');
        $property->setAccessible(TRUE);
        $this->assertSame('none', $property->getValue($this->class));
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testStripSpaces()
    {
        $return = $this->class->strip_link_spaces('hello world');
        $this->assertSame('hello-world', $return);
    }

    /**
     * Provide HTTP status codes
     */
    public function responseStatusProvider()
    {
        $return = [];

        $return[] = [200, 'text-success'];
        $return[] = [204, 'text-success'];
        $return[] = [304, 'text-warning'];
        $return[] = [404, 'text-error'];
        $return[] = [501, 'text-error'];

        return $return;
    }

    /**
     * Test if the value the class is initialized with is correct
     *
     * @dataProvider responseStatusProvider
     *
     * @param int    $code HTTP code
     * @param string $text Class to return
     */
    public function testResponseStatus($code, $text)
    {
        $return = $this->class->get_response_status($code);
        $this->assertSame($text, $return);
    }

    /**
     * Provide HTTP methods
     */
    public function requestMethodProvider()
    {
        $return = [];

        $return[] = ['POST', 'fas fa-plus-square POST'];
        $return[] = ['post', 'fas fa-plus-square POST'];
        $return[] = ['get', 'fas fa-arrow-circle-down GET'];
        $return[] = ['put', 'fas fa-pencil-square PUT'];
        $return[] = ['delete', 'fas fa-minus-square DELETE'];
        $return[] = ['head', 'fas HEAD'];
        $return[] = ['options', 'fas OPTIONS'];
        $return[] = ['PATCH', 'fas PATCH'];

        return $return;
    }

    /**
     * Test if the value the class is initialized with is correct
     *
     * @dataProvider requestMethodProvider
     *
     * @param int    $code HTTP Method
     * @param string $text Class to return
     */
    public function testRequestMethod($code, $text)
    {
        $return = $this->class->get_method_icon($code);
        $this->assertSame($text, $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testIncludeFileDefault()
    {
        $return = $this->class->find_include_file('default');
        $this->assertSame('PHPDraft/Out/HTML/default.phtml', $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testIncludeFileFallback()
    {
        $return = $this->class->find_include_file('gfsdfdsf');
        $this->assertSame('PHPDraft/Out/HTML/default.phtml', $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testIncludeFileNone()
    {
        $return = $this->class->find_include_file('gfsdfdsf', 'xyz');
        $this->assertSame(NULL, $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testIncludeFileSingle()
    {
        set_include_path(TEST_STATICS . '/include_single:' . get_include_path());
        $return = $this->class->find_include_file('hello', 'txt');
        $this->assertSame('hello.txt', $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testIncludeFileMultiple()
    {
        set_include_path(TEST_STATICS . '/include_folders:' . get_include_path());
        $return = $this->class->find_include_file('hello', 'txt');
        $this->assertSame('hello/hello.txt', $return);

        $return = $this->class->find_include_file('test', 'txt');
        $this->assertSame('templates/test.txt', $return);

        $return = $this->class->find_include_file('text', 'txt');
        $this->assertSame('templates/text/text.txt', $return);
    }
}