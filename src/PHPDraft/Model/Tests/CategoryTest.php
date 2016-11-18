<?php
/**
 * This file contains the CategoryTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\Model\Category;
use ReflectionClass;

/**
 * Class CategoryTest
 * @covers PHPDraft\Model\Category
 */
class CategoryTest extends BaseTest
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