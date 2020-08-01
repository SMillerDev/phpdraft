<?php

/**
 * This file contains the ResourceTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\HierarchyElement;
use PHPDraft\Model\Resource;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;

/**
 * Class ResourceTest
 * @covers \PHPDraft\Model\Resource
 */
class ResourceTest extends LunrBaseTest
{
    /**
     * Mock of the parent class
     *
     * @var HierarchyElement|MockObject
     */
    protected $parent;

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->parent     = $this->getMockBuilder('\PHPDraft\Model\Category')
                                 ->getMock();
        $this->class      = new Resource($this->parent);
        $this->reflection = new ReflectionClass('PHPDraft\Model\Resource');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly(): void
    {
        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalled(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"attributes":{"href": "something", "hrefVariables":{"content": [{}]}}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(true);
        $this->assertSame('something', $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledNoHREF(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"content":[]}';

        $this->class->parse(json_decode($obj));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(true);
        $this->assertNull($href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsCopy(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"content":[{"element":"copy", "content":""},{"element":"hello", "content":""}, {"element":"hello", "content":""}]}';

        $this->class->parse(json_decode($obj));

        $this->assertNull($this->get_reflection_property_value('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsNotCopy(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);
        $this->assertEmpty($this->get_reflection_property_value('children'));

        $obj = '{"content":[{"element":"hello", "content":""}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertNotEmpty($this->get_reflection_property_value('children'));
    }
}
