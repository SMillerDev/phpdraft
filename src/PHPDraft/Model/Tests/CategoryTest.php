<?php
/**
 * This file contains the CategoryTest.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;


use PHPDraft\Core\TestBase;
use PHPDraft\Model\Category;
use ReflectionClass;

class CategoryTest extends TestBase
{
    /**
     * Set up
     */
    public function setUp()
    {
        $this->class      = new Category();
        $this->reflection = new ReflectionClass('PHPDraft\Model\Category');
    }

    /**
     * Tear down
     */
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
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $this->assertNull($property->getValue($this->class));
    }
}