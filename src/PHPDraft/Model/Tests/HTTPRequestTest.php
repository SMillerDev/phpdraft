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
        $property = $this->reflection->getProperty('method');
        $property->setAccessible(true);
        $property->setValue($this->class, 200);

        $obj = '{"method":200, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnDesc(): void
    {
        $property = $this->reflection->getProperty('body');
        $property->setAccessible(true);
        $property->setValue($this->class, 'hello');

        $obj = '{"method":300, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnHeaders(): void
    {
        $property = $this->reflection->getProperty('headers');
        $property->setAccessible(true);
        $property->setValue($this->class, []);

        $obj = '{"method":300, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnBoth(): void
    {
        $s_property = $this->reflection->getProperty('method');
        $s_property->setAccessible(true);
        $s_property->setValue($this->class, 200);

        $property = $this->reflection->getProperty('body');
        $property->setAccessible(true);
        $property->setValue($this->class, 'hello');

        $obj = '{"method":200, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertTrue($return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandNoKey(): void
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(true);
        $property->setValue($this->class, $this->parent);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -X \'\'', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandWithHeaders(): void
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(true);
        $property->setValue($this->class, $this->parent);

        $headers = ['header' => 'value'];
        $property = $this->reflection->getProperty('headers');
        $property->setAccessible(true);
        $property->setValue($this->class, $headers);

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
        $property = $this->reflection->getProperty('body');
        $property->setAccessible(true);
        $property->setValue($this->class, ['this', 'is', 'a', 'body']);

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
        $property = $this->reflection->getProperty('body');
        $property->setAccessible(true);
        $property->setValue($this->class, 1000);

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

        $property = $this->reflection->getProperty('struct');
        $property->setAccessible(true);
        $property->setValue($this->class, $struct);

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

        $href_property = $this->reflection->getProperty('method');
        $href_property->setAccessible(true);
        $this->assertSame('TEST', $href_property->getValue($this->class));
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

        $href_property = $this->reflection->getProperty('headers');
        $href_property->setAccessible(true);
        $this->assertSame(['KEY' => 'VALUE'], $href_property->getValue($this->class));
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

        $struct_property = $this->reflection->getProperty('struct');
        $struct_property->setAccessible(true);
        $this->assertSame([], $struct_property->getValue($this->class));

        $body_property = $this->reflection->getProperty('body');
        $body_property->setAccessible(true);
        $this->assertSame([], $body_property->getValue($this->class));
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

        $struct_property = $this->reflection->getProperty('struct');
        $struct_property->setAccessible(true);
        $this->assertSame([], $struct_property->getValue($this->class));

        $body_property = $this->reflection->getProperty('description');
        $body_property->setAccessible(true);
        $this->assertSame('<p>text</p>' . PHP_EOL, $body_property->getValue($this->class));
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

        $struct_property = $this->reflection->getProperty('struct');
        $struct_property->setAccessible(true);
        $this->assertNotEmpty($struct_property->getValue($this->class));
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

        $struct_property = $this->reflection->getProperty('body');
        $struct_property->setAccessible(true);
        $this->assertSame(['something'], $struct_property->getValue($this->class));

        $header_property = $this->reflection->getProperty('headers');
        $header_property->setAccessible(true);
        $this->assertSame(['Content-Type' => ''], $header_property->getValue($this->class));
    }
}
