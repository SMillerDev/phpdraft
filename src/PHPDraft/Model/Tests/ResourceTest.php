<?php

/**
 * This file contains the ResourceTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Model\Category;
use PHPDraft\Model\HierarchyElement;
use PHPDraft\Model\Resource;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ResourceTest
 */
#[CoversClass(Resource::class)]
class ResourceTest extends LunrBaseTestCase
{
    private Resource $class;

    /**
     * Mock of the parent class
     *
     * @var Category&MockObject
     */
    protected Category&MockObject $parent;

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->parent     = $this->getMockBuilder('\PHPDraft\Model\Category')
                                 ->getMock();

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
        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalled(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"attributes":{"href": "something", "hrefVariables":{"content": [{}]}}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertPropertyEquals('href', 'something');
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledNoHREF(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);
        $this->setReflectionPropertyValue('href', null);

        $obj = '{"content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertNull($this->getReflectionPropertyValue('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsCopy(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);
        $this->setReflectionPropertyValue('href', null);

        $obj = '{"content":[{"element":"copy", "content":""},{"element":"hello", "content":""}, {"element":"hello", "content":""}]}';

        $this->class->parse(json_decode($obj));

        $this->assertNull($this->getReflectionPropertyValue('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsNotCopy(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);
        $this->setReflectionPropertyValue('href', null);
        $this->assertEmpty($this->getReflectionPropertyValue('children'));

        $obj = '{"content":[{"element":"hello", "content":""}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertNotEmpty($this->getReflectionPropertyValue('children'));
    }
}
