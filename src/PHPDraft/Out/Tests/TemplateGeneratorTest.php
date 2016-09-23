<?php
/**
 * This file contains the TemplateGeneratorTest.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out\Tests;


use PHPDraft\Core\BaseTest;
use PHPDraft\Out\TemplateGenerator;

class TemplateGeneratorTest extends BaseTest
{
    public function setUp()
    {
        $this->class      = new TemplateGenerator('default', 'none');
        $this->reflection = new \ReflectionClass('PHPDraft\Out\TemplateGenerator');
    }

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
        $property = $this->reflection->getProperty('template');
        $property->setAccessible(TRUE);
        $this->assertSame('default', $property->getValue($this->class));
        $property = $this->reflection->getProperty('image');
        $property->setAccessible(TRUE);
        $this->assertSame('none', $property->getValue($this->class));
    }
}