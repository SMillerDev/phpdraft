<?php
/**
 * This file contains the ArrayStructureTest.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements\Tests;


use PHPDraft\Core\BaseTest;
use PHPDraft\Model\Elements\ArrayStructureElement;

class ArrayStructureTest extends BaseTest
{
    public function setUp()
    {
        $this->class      = new ArrayStructureElement();
        $this->reflection = new \ReflectionClass('PHPDraft\Model\Elements\ArrayStructureElement');
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
        $property = $this->reflection->getProperty('element');
        $property->setAccessible(TRUE);
        $this->assertNull($property->getValue($this->class));
    }
}