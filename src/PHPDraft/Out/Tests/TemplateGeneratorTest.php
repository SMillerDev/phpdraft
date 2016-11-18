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
 * @covers PHPDraft\Out\TemplateGenerator
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
}