<?php

/**
 * This file contains the HTTPRequestTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Model\HierarchyElement;
use PHPDraft\Model\HTTPRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class HTTPRequestTest
 */
#[CoversClass(HttpRequest::class)]
class HTTPRequestTest extends LunrBaseTestCase
{
    private HTTPRequest $class;

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
        $parent           = $this->getMockBuilder('\PHPDraft\Model\Transition')
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $this->parent     = $this->getMockBuilder('\PHPDraft\Model\Transition')
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $this->mockFunction('microtime', fn() => '1000');
        $this->class      = new HTTPRequest($parent);
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
        $this->assertIsObject($this->getReflectionPropertyValue('parent'));
        $this->assertEquals('a9b7ba70783b617e9998dc4dd82eb3c5', $this->getReflectionPropertyValue('id'));
    }

    /**
     * Tests if get_id returns the correct ID.
     */
    public function testGetId(): void
    {
        $this->assertSame('a9b7ba70783b617e9998dc4dd82eb3c5', $this->class->get_id());
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnStatusCode(): void
    {
        $this->setReflectionPropertyValue('method', 'POST');

        $obj = '{"method":200, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnDesc(): void
    {
        $this->setReflectionPropertyValue('body', 'hello');

        $obj = '{"method":300, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnHeaders(): void
    {
        $this->setReflectionPropertyValue('headers', []);

        $obj = '{"method":300, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnBoth(): void
    {
        $this->setReflectionPropertyValue('method', 'GET');
        $this->setReflectionPropertyValue('title', 'hello');

        $obj = '{"attributes":{"method": "GET"}, "meta":{"title":"hello"}}';
        $b   = new HTTPRequest($this->parent);
        $b->parse(json_decode($obj));

        $this->assertTrue($this->class->is_equal_to($b));
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandNoKey(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);
        $this->setReflectionPropertyValue('method', '');

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -X \'\'', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandWithHeaders(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);
        $this->setReflectionPropertyValue('method', '');

        $headers = ['header' => 'value'];
        $this->setReflectionPropertyValue('headers', $headers);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -X -H \'header: value\' \'\'', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandStringBody(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);
        $this->setReflectionPropertyValue('body', 'body');
        $this->setReflectionPropertyValue('method', 'GET');

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -XGET --data-binary \'body\' \'\'', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandArrayBody(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);
        $this->setReflectionPropertyValue('method', 'GET');
        $this->setReflectionPropertyValue('body', ['this', 'is', 'a', 'body']);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -XGET --data-binary \'thisisabody\' \'\'', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandStructBodyFilled(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);
        $this->setReflectionPropertyValue('method', 'GET');
        $this->setReflectionPropertyValue('body', 1000);

        $struct = $this->getMockBuilder('\PHPDraft\Model\Elements\ObjectStructureElement')
                       ->disableOriginalConstructor()
                       ->getMock();
        $struct_ar = $this->getMockBuilder('\PHPDraft\Model\Elements\RequestBodyElement')
                          ->disableOriginalConstructor()
                          ->getMock();

        $struct_ar->expects($this->once())
                  ->method('print_request')
                  ->with(null)
                  ->willReturn('TEST');

        $struct->value = [ $struct_ar ];
        $this->setReflectionPropertyValue('struct', $struct);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -XGET --data-binary \'TEST\' \'\'', $return);
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalled(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"attributes":{"method":"TEST"}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('TEST', $this->getReflectionPropertyValue('method'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithHeaders(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"attributes":{"method":"TEST", "headers":{"content":[{"content":{"key":{"content":"KEY"}, "value":{"content":"VALUE"}}}]}}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame(['KEY' => 'VALUE'], $this->getReflectionPropertyValue('headers'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithPOST(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"attributes":{"method":"POST"}, "content":[{"element":"gold"}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame([], $this->getReflectionPropertyValue('struct'));
        $this->assertSame([], $this->getReflectionPropertyValue('body'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithPOSTCopy(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"attributes":{"method":"POST"}, "content":[{"element":"copy", "content":"text"}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertEquals([], $this->getReflectionPropertyValue('struct'));
        $this->assertEquals('text', $this->getReflectionPropertyValue('description'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithPOSTStruct(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"attributes":{"method":"POST"}, "content":[{"element":"dataStructure", "content": {}}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertNotEmpty($this->getReflectionPropertyValue('struct'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithPOSTAsset(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"attributes":{"method":"POST"}, "content":[{"content":"something", "element":"asset", "meta":{"classes":["messageBody"]}}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame(['something'], $this->getReflectionPropertyValue('body'));
        $this->assertSame(['Content-Type' => ''], $this->getReflectionPropertyValue('headers'));
    }
}
