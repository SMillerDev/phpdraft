<?php

/**
 * This file contains the HierarchyElementChildTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\HierarchyElement;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class HierarchyElementChildTest
 * @package PHPDraft\Model\Tests
 */
class HierarchyElementChildTest extends LunrBaseTest
{
    /**
     * Mock of the parent class
     *
     * @var HierarchyElement|MockObject
     */
    protected $parent;

    public function setUp(): void
    {
        $this->parent = $this->getMockBuilder('\PHPDraft\Model\HierarchyElement')
                             ->getMock();
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testChildrenSetup(): void
    {
        $this->assertSame([], $this->class->children);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly(): void
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(true);
        $this->assertNull($property->getValue($this->class));
    }
}
