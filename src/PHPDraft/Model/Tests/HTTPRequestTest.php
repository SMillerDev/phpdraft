<?php

/**
 * This file contains the HTTPRequestTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\HierarchyElement;
use PHPDraft\Model\HTTPRequest;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;

/**
 * Class HTTPRequestTest
 * @covers \PHPDraft\Model\HTTPRequest
 */
class HTTPRequestTest extends LunrBaseTest
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
        $parent           = $this->createMock('\PHPDraft\Model\Transition');

        $this->parent     = $this->getMockBuilder('\PHPDraft\Model\Transition')
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $this->mock_function('microtime', function () {
            return '1000';
        });
        $this->class      = new HTTPRequest($parent);
        $this->unmock_function('microtime');
        $this->reflection = new ReflectionClass('PHPDraft\Model\HTTPRequest');
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
        $this->assertIsObject($this->get_reflection_property_value('parent'));
        $this->assertEquals('a9b7ba70783b617e9998dc4dd82eb3c5', $this->get_reflection_property_value('id'));
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
        $this->set_reflection_property_value('method', 'POST');

        $obj = '{"method":200, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnDesc(): void
    {
        $this->set_reflection_property_value('body', 'hello');

        $obj = '{"method":300, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnHeaders(): void
    {
        $this->set_reflection_property_value('headers', []);

        $obj = '{"method":300, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnBoth(): void
    {
        $this->set_reflection_property_value('method', 'GET');
        $this->set_reflection_property_value('title', 'hello');

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
        $this->set_reflection_property_value('parent', $this->parent);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -X \'\'', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandWithHeaders(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $headers = ['header' => 'value'];
        $this->set_reflection_property_value('headers', $headers);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -X -H \'header: value\' \'\'', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandStringBody(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);
        $this->set_reflection_property_value('body', 'body');
        $this->set_reflection_property_value('method', 'GET');

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -XGET --data-binary \'body\' \'\'', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandArrayBody(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);
        $this->set_reflection_property_value('method', 'GET');
        $this->set_reflection_property_value('body', ['this', 'is', 'a', 'body']);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -XGET --data-binary \'thisisabody\' \'\'', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandStructBodyFilled(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);
        $this->set_reflection_property_value('method', 'GET');
        $this->set_reflection_property_value('body', 1000);

        $struct = $this->getMockBuilder('\PHPDraft\Model\Elements\ObjectStructureElement')
                       ->disableOriginalConstructor()
                       ->getMock();
        $struct_ar = $this->getMockBuilder('\PHPDraft\Model\Elements\RequestBodyElement')
                          ->disableOriginalConstructor()
                          ->getMock();

        $struct_ar->expects($this->once())
                  ->method('print_request')
                  ->with(null)
                  ->will($this->returnValue('TEST'));

        $struct->value = [ $struct_ar ];
        $this->set_reflection_property_value('struct', $struct);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -XGET --data-binary \'TEST\' \'\'', $return);
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalled(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"attributes":{"method":"TEST"}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertSame('TEST', $this->get_reflection_property_value('method'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithHeaders(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"attributes":{"method":"TEST", "headers":{"content":[{"content":{"key":{"content":"KEY"}, "value":{"content":"VALUE"}}}]}}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertSame(['KEY' => 'VALUE'], $this->get_reflection_property_value('headers'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithPOST(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"attributes":{"method":"POST"}, "content":[{"element":"gold"}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertSame([], $this->get_reflection_property_value('struct'));
        $this->assertSame([], $this->get_reflection_property_value('body'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithPOSTCopy(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"attributes":{"method":"POST"}, "content":[{"element":"copy", "content":"text"}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertEquals([], $this->get_reflection_property_value('struct'));
        $this->assertEquals('text', $this->get_reflection_property_value('description'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithPOSTStruct(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"attributes":{"method":"POST"}, "content":[{"element":"dataStructure", "content": {}}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertNotEmpty($this->get_reflection_property_value('struct'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithPOSTAsset(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = '{"attributes":{"method":"POST"}, "content":[{"content":"something", "element":"asset", "meta":{"classes":["messageBody"]}}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertSame(['something'], $this->get_reflection_property_value('body'));
        $this->assertSame(['Content-Type' => ''], $this->get_reflection_property_value('headers'));
    }
}
