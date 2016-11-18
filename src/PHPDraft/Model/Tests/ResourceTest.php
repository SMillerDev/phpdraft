<?php
/**
 * This file contains the ResourceTest.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;


use PHPDraft\Core\BaseTest;
use PHPDraft\Model\Resource;
use ReflectionClass;

/**
 * Class ResourceTest
 * @covers PHPDraft\Model\Resource
 */
class ResourceTest extends BaseTest
{

    /**
     * Set up
     */
    public function setUp()
    {
        $parent           = NULL;
        $this->class      = new Resource($parent);
        $this->reflection = new ReflectionClass('PHPDraft\Model\Resource');
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