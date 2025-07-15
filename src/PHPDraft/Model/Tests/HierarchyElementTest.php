<?php

/**
 * This file contains the HierarchyElementTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Model\HierarchyElement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class HierarchyElementTest
 */
#[CoversClass(HierarchyElement::class)]
class HierarchyElementTest extends LunrBaseTestCase
{
    private HierarchyElement $class;

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

        $this->parent     = $this->getMockBuilder('\PHPDraft\Model\Transition')
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $this->class      = new class extends HierarchyElement {
        };
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
        $this->assertPropertyEquals('parent', null);
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalled(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"meta":{"title":"TEST"}, "content":""}';

        $this->class->parse(json_decode($obj));

        $this->assertPropertySame('parent', $this->parent);
        $this->assertPropertySame('title', 'TEST');
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledLoop(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"meta":{"title":"TEST"}, "content":[{"element":"copy", "content":"hello"}]}';

        $this->class->parse(json_decode($obj));

        $this->assertPropertySame('parent', $this->parent);
        $this->assertPropertySame('title', 'TEST');
        $this->assertPropertySame('description', 'hello');
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledSlice(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"meta":{"title":"TEST"}, "content":[{"element":"copy", "content":"hello"}, {"element":"test", "content":"hello"}]}';

        $this->class->parse(json_decode($obj));

        $this->assertPropertySame('parent', $this->parent);
        $this->assertPropertySame('title', 'TEST');
        $this->assertPropertySame('description', 'hello');
    }


    /**
     * Test basic get_href
     */
    public function testGetHrefIsCalledWithParent(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);
        $this->setReflectionPropertyValue('title', 'title');

        $this->parent->expects($this->once())
                     ->method('get_href')
                     ->willReturn('hello');

        $result = $this->class->get_href();

        $this->assertSame($result, 'hello-title');
    }

    /**
     * Test basic get_href
     */
    public function testGetHrefIsCalledWithoutParent(): void
    {
        $this->setReflectionPropertyValue('title', 'title');
        $result = $this->class->get_href();

        $this->assertSame($result, 'title');
    }

    /**
     * Test basic get_href
     */
    public function testGetHrefIsCalledWithTitleWithSpaces(): void
    {
        $this->setReflectionPropertyValue('title', 'some title');
        $this->setReflectionPropertyValue('parent', $this->parent);

        $this->parent->expects($this->once())
                     ->method('get_href')
                     ->willReturn('hello');

        $result = $this->class->get_href();

        $this->assertSame($result, 'hello-some-title');
    }
}
