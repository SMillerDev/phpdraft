<?php
/**
 * This file contains the HTTPRequestTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\Model\HierarchyElement;
use PHPDraft\Model\HTTPRequest;
use ReflectionClass;

/**
 * Class HTTPRequestTest
 * @covers \PHPDraft\Model\HTTPRequest
 */
class HTTPRequestTest extends BaseTest
{
    /**
     * Mock of the parent class
     *
     * @var HierarchyElement
     */
    protected $parent;

    /**
     * Set up
     */
    public function setUp()
    {
        $parent           = NULL;

        $this->parent     = $this->getMockBuilder('\PHPDraft\Model\Transition')
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $this->mock_function('microtime', 'test');
        $this->class      = new HTTPRequest($parent);
        $this->unmock_function('microtime');
        $this->reflection = new ReflectionClass('PHPDraft\Model\HTTPRequest');
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly()
    {
        $parent_property = $this->reflection->getProperty('parent');
        $parent_property->setAccessible(TRUE);
        $this->assertNull($parent_property->getValue($this->class));

        $id_property = $this->reflection->getProperty('id');
        $id_property->setAccessible(TRUE);
        $this->assertSame('098f6bcd4621d373cade4e832627b4f6', $id_property->getValue($this->class));
    }

    /**
     * Tests if get_id returns the correct ID.
     */
    public function testGetId()
    {
        $this->assertSame('098f6bcd4621d373cade4e832627b4f6', $this->class->get_id());
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnStatusCode()
    {
        $property = $this->reflection->getProperty('method');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, 200);

        $obj = '{"method":200, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnDesc()
    {
        $property = $this->reflection->getProperty('body');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, 'hello');

        $obj = '{"method":300, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnHeaders()
    {
        $property = $this->reflection->getProperty('headers');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, []);

        $obj = '{"method":300, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnBoth()
    {
        $s_property = $this->reflection->getProperty('method');
        $s_property->setAccessible(TRUE);
        $s_property->setValue($this->class, 200);

        $property = $this->reflection->getProperty('body');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, 'hello');

        $obj = '{"method":200, "body":"hello", "headers":[]}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertTrue($return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandNoKey()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -X \'\'', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandWithHeaders()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $headers = ['header'=>'value'];
        $property = $this->reflection->getProperty('headers');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $headers);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -X -H \'header: value\' \'\'', $return);
    }

    /**
     * Test basic get_hurl_link functions
     */
    public function testGetHurlWithHeaders()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $headers = ['header'=>'value'];
        $property = $this->reflection->getProperty('headers');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $headers);

        $return = $this->class->get_hurl_link('https://ur.l');

        $this->assertSame('https://www.hurl.it/?url=&method=&headers=%7B%22header%22%3A%5B%22value%22%5D%7D', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandStringBody()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);
        $property = $this->reflection->getProperty('body');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, 'body');

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -X --data-binary \'body\' \'\'', $return);
    }

    /**
     * Test basic get_hurl_link functions
     */
    public function testGetHurlStringBody()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);
        $property = $this->reflection->getProperty('body');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, 'body');

        $return = $this->class->get_hurl_link('https://ur.l');

        $this->assertSame('https://www.hurl.it/?url=&method=&body=body', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandArrayBody()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);
        $property = $this->reflection->getProperty('body');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, ['this', 'is', 'a', 'body']);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -X --data-binary \'thisisabody\' \'\'', $return);
    }

    /**
     * Test basic get_hurl_link functions
     */
    public function testGetHurlArrayBody()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);
        $property = $this->reflection->getProperty('body');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, ['this', 'is', 'a', 'body']);

        $return = $this->class->get_hurl_link('https://ur.l');

        $this->assertSame('https://www.hurl.it/?url=&method=&body=this%2Cis%2Ca%2Cbody', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandStructBodyFilled()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);
        $property = $this->reflection->getProperty('body');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, 1000);

        $struct = $this->getMockBuilder('\PHPDraft\Model\Elements\ObjectStructureElement')
                       ->disableOriginalConstructor()
                       ->getMock();
        $struct_ar = $this->getMockBuilder('\PHPDraft\Model\Elements\RequestBodyElement')
                          ->disableOriginalConstructor()
                          ->getMock();

        $struct_ar->expects($this->once())
                  ->method('print_request')
                  ->with(NULL)
                  ->will($this->returnValue('TEST'));

        $struct->value = [ $struct_ar ];

        $property = $this->reflection->getProperty('struct');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $struct);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl -X --data-binary \'TEST\' \'\'', $return);
    }

    /**
     * Test basic get_hurl_link functions
     */
    public function testGetHurlStructBodyFilled()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);
        $property = $this->reflection->getProperty('body');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, 1000);

        $struct = $this->getMockBuilder('\PHPDraft\Model\Elements\ObjectStructureElement')
                       ->disableOriginalConstructor()
                       ->getMock();
        $struct_ar = $this->getMockBuilder('\PHPDraft\Model\Elements\RequestBodyElement')
                          ->disableOriginalConstructor()
                          ->getMock();

        $struct_ar->expects($this->once())
                  ->method('print_request')
                  ->with(NULL)
                  ->will($this->returnValue('TEST'));

        $struct->value = [ $struct_ar ];

        $property = $this->reflection->getProperty('struct');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $struct);

        $return = $this->class->get_hurl_link('https://ur.l');

        $this->assertSame('https://www.hurl.it/?url=&method=&body=TEST', $return);
    }

    /**
     * Test basic get_hurl_link functions
     */
    public function testGetHurlUrlArgs()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);
        $property = $this->reflection->getProperty('method');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, 'GET');

        $this->parent->expects($this->once())
                     ->method('build_url')
                     ->with('https://ur.l')
                     ->will($this->returnValue('http://ur.l/index?lang=nl&key=value'));


        $return = $this->class->get_hurl_link('https://ur.l');

        $this->assertSame('https://www.hurl.it/?args=%7B%22lang%22%3A%5B%22nl%22%5D%2C%22key%22%3A%5B%22value%22%5D%7D&url=http%3A%2F%2Fur.l%2Findex&method=GET', $return);
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalled()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"method":"TEST"}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('method');
        $href_property->setAccessible(TRUE);
        $this->assertSame('TEST', $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithHeaders()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"method":"TEST", "headers":{"content":[{"content":{"key":{"content":"KEY"}, "value":{"content":"VALUE"}}}]}}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('headers');
        $href_property->setAccessible(TRUE);
        $this->assertSame(['KEY'=>'VALUE'], $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithPOST()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"method":"POST"}, "content":[{"element":"gold"}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $struct_property = $this->reflection->getProperty('struct');
        $struct_property->setAccessible(TRUE);
        $this->assertSame([], $struct_property->getValue($this->class));

        $body_property = $this->reflection->getProperty('body');
        $body_property->setAccessible(TRUE);
        $this->assertSame([], $body_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithPOSTCopy()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"method":"POST"}, "content":[{"element":"copy", "content":"text"}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $struct_property = $this->reflection->getProperty('struct');
        $struct_property->setAccessible(TRUE);
        $this->assertSame([], $struct_property->getValue($this->class));

        $body_property = $this->reflection->getProperty('description');
        $body_property->setAccessible(TRUE);
        $this->assertSame('<p>text</p>'.PHP_EOL, $body_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithPOSTStruct()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"method":"POST"}, "content":[{"element":"dataStructure"}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $struct_property = $this->reflection->getProperty('struct');
        $struct_property->setAccessible(TRUE);
        $this->assertNotEmpty($struct_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWithPOSTAsset()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"method":"POST"}, "content":[{"content":"something", "element":"asset", "meta":{"classes":["messageBody"]}}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $struct_property = $this->reflection->getProperty('body');
        $struct_property->setAccessible(TRUE);
        $this->assertSame(['something'], $struct_property->getValue($this->class));

        $header_property = $this->reflection->getProperty('headers');
        $header_property->setAccessible(TRUE);
        $this->assertSame(['Content-Type'=>''], $header_property->getValue($this->class));
    }
}