<?php
/**
 * This file contains the HierarchyElementTest.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;


use PHPDraft\Core\BaseTest;
use ReflectionClass;

class HierarchyElementTest extends BaseTest
{
    /**
     * Set up
     */
    public function setUp()
    {
        $this->class      = $this->getMockForAbstractClass('PHPDraft\Model\HierarchyElement');
        $this->reflection = new ReflectionClass('PHPDraft\Model\HierarchyElement');
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