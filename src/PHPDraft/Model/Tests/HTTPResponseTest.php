<?php

/**
 * This file contains the HTTPResponseTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\HierarchyElement;
use PHPDraft\Model\HTTPResponse;
use PHPDraft\Model\Transition;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;

/**
 * Class HTTPResponseTest
 * @covers \PHPDraft\Model\HTTPResponse
 */
class HTTPResponseTest extends LunrBaseTest
{
    /**
     * Mock of the parent class
     *
     * @var HierarchyElement|MockObject
     */
    protected $parent;

    /**
     * Mock of the parent class
     *
     * @var Transition|MockObject
     */
    protected $parent_transition;

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->parent_transition = $this->createMock('\PHPDraft\Model\Transition');
        $this->parent            = $this->getMockBuilder('\PHPDraft\Model\HierarchyElement')
                                        ->getMock();
        $this->mock_function('microtime', function () {
            return '1000';
        });
        $this->class      = new HTTPResponse($this->parent_transition);
        $this->unmock_function('microtime');
        $this->reflection = new ReflectionClass('\PHPDraft\Model\HTTPResponse');
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
        $this->assertSame($this->parent_transition, $this->get_reflection_property_value('parent'));
        $this->assertSame('a9b7ba70783b617e9998dc4dd82eb3c5', $this->get_reflection_property_value('id'));
    }

    /**
     * Tests if get_id returns the correct ID.
     */
    public function testGetId(): void
    {
        $this->assertSame('a9b7ba70783b617e9998dc4dd82eb3c5', $this->class->get_id());
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalled(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"attributes":{"statusCode":1000, "headers":{"content":[]}}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertSame(1000, $this->get_reflection_property_value('statuscode'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledExtraHeaders(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"attributes":{"statusCode":1000, "headers":{"content":[{"content":{"key":{"content":"contentKEY"}, "value":{"content":"contentVALUE"}}}]}}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertSame(['contentKEY' => 'contentVALUE'], $this->get_reflection_property_value('headers'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWOAttributes(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertNull($this->get_reflection_property_value('statuscode'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledCopyContent(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"content":[{"element":"copy", "content":""}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertSame('', $this->get_reflection_property_value('description'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledStructContentEmpty(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"content":[{"element":"dataStructure", "content":{"content": {}}}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertEmpty($this->get_reflection_property_value('structure'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledStructContent(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"content":[{"element":"dataStructure", "content":{"content": [{}]}}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertNotEmpty($this->get_reflection_property_value('structure'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledStructContentHasAttr(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"content":[{"content":"hello", "attributes":{"contentType":"content"}, "element":"asset"}]}';

        $this->class->parse(json_decode($obj));
        $prop = $this->get_reflection_property_value('content');
        $this->assertArrayHasKey('content', $prop);
        $this->assertSame('hello', $prop['content']);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnStatusCode(): void
    {
        $this->set_reflection_property_value('statuscode', 200);

        $obj = '{"statuscode":200, "description":"hello"}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnDesc(): void
    {
        $this->set_reflection_property_value('description', 'hello');

        $obj = '{"statuscode":300, "description":"hello"}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnBoth(): void
    {
        $this->set_reflection_property_value('statuscode', 200);
        $this->set_reflection_property_value('description', 'hello');

        $obj = '{"attributes":{"statusCode":200}, "content":[{"element":"copy", "content": "hello"}]}';
        $b = new HTTPResponse($this->parent_transition);
        $b->parse(json_decode($obj));

        $return = $this->class->is_equal_to($b);

        $this->assertTrue($return);
    }
}
