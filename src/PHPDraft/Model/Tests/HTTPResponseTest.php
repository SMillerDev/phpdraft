<?php

/**
 * This file contains the HTTPResponseTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Model\HierarchyElement;
use PHPDraft\Model\HTTPResponse;
use PHPDraft\Model\Transition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class HTTPResponseTest
 */
#[CoversClass(HttpResponse::class)]
class HTTPResponseTest extends LunrBaseTestCase
{
    private HTTPResponse $class;

    /**
     * Mock of the parent class
     *
     * @var HierarchyElement|MockObject
     */
    protected mixed $parent;

    /**
     * Mock of the parent class
     *
     * @var Transition|MockObject
     */
    protected mixed $parent_transition;

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->parent_transition = $this->getMockBuilder('\PHPDraft\Model\Transition')
                                        ->disableOriginalConstructor()
                                        ->getMock();
        $this->parent            = $this->getMockBuilder('\PHPDraft\Model\Transition')
                                        ->disableOriginalConstructor()
                                        ->getMock();
        $this->mockFunction('microtime', fn() => '1000');
        $this->class      = new HTTPResponse($this->parent_transition);
        $this->unmockFunction('microtime');
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
        $this->assertSame($this->parent_transition, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('a9b7ba70783b617e9998dc4dd82eb3c5', $this->getReflectionPropertyValue('id'));
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
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"attributes":{"statusCode":1000, "headers":{"content":[]}}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame(1000, $this->getReflectionPropertyValue('statuscode'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledExtraHeaders(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"attributes":{"statusCode":1000, "headers":{"content":[{"content":{"key":{"content":"contentKEY"}, "value":{"content":"contentVALUE"}}}]}}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame(['contentKEY' => 'contentVALUE'], $this->getReflectionPropertyValue('headers'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWOAttributes(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);
        $this->setReflectionPropertyValue('statuscode', 200);

        $obj = '{"content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame($this->getReflectionPropertyValue('statuscode'), 200);
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledCopyContent(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"content":[{"element":"copy", "content":""}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('', $this->getReflectionPropertyValue('description'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledStructContentEmpty(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"content":[{"element":"dataStructure", "content":{"content": {}}}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertEmpty($this->getReflectionPropertyValue('structure'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledStructContent(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"content":[{"element":"dataStructure", "content":{"content": [{}]}}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertNotEmpty($this->getReflectionPropertyValue('structure'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledStructContentHasAttr(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"content":[{"content":"hello", "attributes":{"contentType":"content"}, "element":"asset"}]}';

        $this->class->parse(json_decode($obj));
        $prop = $this->getReflectionPropertyValue('content');
        $this->assertArrayHasKey('content', $prop);
        $this->assertSame('hello', $prop['content']);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnStatusCode(): void
    {
        $this->setReflectionPropertyValue('statuscode', 200);

        $obj = '{"statuscode":200, "description":"hello"}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnDesc(): void
    {
        $this->setReflectionPropertyValue('description', 'hello');

        $obj = '{"statuscode":300, "description":"hello"}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnBoth(): void
    {
        $this->setReflectionPropertyValue('statuscode', 200);
        $this->setReflectionPropertyValue('description', 'hello');

        $obj = '{"attributes":{"statusCode":200}, "content":[{"element":"copy", "content": "hello"}]}';
        $b = new HTTPResponse($this->parent_transition);
        $b->parse(json_decode($obj));

        $return = $this->class->is_equal_to($b);

        $this->assertTrue($return);
    }
}
