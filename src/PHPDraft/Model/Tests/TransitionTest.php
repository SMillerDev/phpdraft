<?php
/**
 * This file contains the TransitionTest.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;


use PHPDraft\Core\BaseTest;
use PHPDraft\Model\Transition;
use ReflectionClass;

/**
 * Class TransitionTest
 * @covers PHPDraft\Model\Transition
 */
class TransitionTest extends BaseTest
{

    /**
     * Set up
     */
    public function setUp()
    {
        $parent           = NULL;
        $this->class      = new Transition($parent);
        $this->reflection = new ReflectionClass('PHPDraft\Model\Transition');
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