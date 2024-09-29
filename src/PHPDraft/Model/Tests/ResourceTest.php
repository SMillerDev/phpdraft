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
    private Resource $class;

    /**
     * Mock of the parent class
     *
     * @var HierarchyElement|MockObject
     */
    protected mixed $parent;

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->parent     = $this->getMockBuilder('\PHPDraft\Model\Category')
                                 ->getMock();

        $this->parent->href = null;
        $this->class      = new Resource($this->parent);
        $this->baseSetUp($this->class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
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

        $this->assertPropertyEquals('href', 'something');
    }

    /**
     * Test basic parse functions
     *
     * @covers \PHPDraft\Model\Resource::parse
     */
    public function testParseIsCalledNoHREF(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);
        $this->set_reflection_property_value('href', null);

        $obj = '{"content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertNull($this->get_reflection_property_value('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsCopy(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);
        $this->set_reflection_property_value('href', null);

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
        $this->set_reflection_property_value('href', null);
        $this->assertEmpty($this->get_reflection_property_value('children'));

        $obj = '{"content":[{"element":"hello", "content":""}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertNotEmpty($this->get_reflection_property_value('children'));
    }
}
