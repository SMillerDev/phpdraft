<?php
/**
 * This file contains the TransitionTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */
namespace PHPDraft\Model\Tests;

use PHPDraft\Model\Transition;
use ReflectionClass;

/**
 * Class TransitionTest
 * @covers \PHPDraft\Model\Transition
 */
class TransitionTest extends HierarchyElementChildTest
{

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->parent     = $this->createMock('\PHPDraft\Model\Resource');
        $this->class      = new Transition($this->parent);
        $this->reflection = new ReflectionClass('PHPDraft\Model\Transition');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->class);
        unset($this->reflection);
        unset($this->parent);
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

        $obj = '{"attributes":{"href":"something"}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertSame('something', $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIssetHrefVariables(): void
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"href":"something", "hrefVariables":"hello"}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertSame('something', $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIssetData(): void
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"href":"something", "data":"hello"}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertSame('something', $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsNotArrayContent(): void
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"href":"something", "data":"hello"}, "content":""}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertSame('something', $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsArrayContentNoChild(): void
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"href":"something", "data":"hello"}, "content":[{"element":"123"}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertSame('something', $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsArrayContentWrongChild(): void
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"href":"something", "data":"hello"}, "content":[{"element":"123", "content":[{"element":"test"}]}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertSame('something', $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsArrayContentRequest(): void
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"href":"something", "data":"hello"}, "content":[{"element":"123", "content":[{"element":"httpRequest", "attributes":{"method":"TEST"}}]}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertSame('something', $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsArrayContentResponse(): void
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"href":"something", "data":"hello"}, "content":[{"element":"123", "content":[{"element":"httpResponse", "content":[], "attributes":{"statusCode":"1000", "headers":{"content":[]}}}]}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertSame('something', $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsArrayContentDefault(): void
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"href":"something", "data":"hello"}, "content":[{"element":"123", "content":[{"element":"Cow", "content":[], "attributes":{"statusCode":"1000", "headers":{"content":[]}}}]}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertSame('something', $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsArrayContentRequestList(): void
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $mock_req = $this->getMockBuilder('\PHPDraft\Model\HTTPRequest')
                         ->disableOriginalConstructor()
                         ->getMock();
        $mock_req->expects($this->once())
                 ->method('is_equal_to')
                 ->will($this->returnValue(TRUE));
        $requests = [$mock_req];
        $req_property = $this->reflection->getProperty('requests');
        $req_property->setAccessible(TRUE);
        $req_property->setValue($this->class, $requests);

        $obj = '{"attributes":{"href":"something", "data":"hello"}, "content":[{"element":"123", "content":[{"element":"httpRequest", "attributes":{"method":"TEST"}}]}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertSame('something', $href_property->getValue($this->class));
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandNoKey(): void
    {
        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandKey(): void
    {
        $mock_req = $this->getMockBuilder('\PHPDraft\Model\HTTPRequest')
                         ->disableOriginalConstructor()
                         ->getMock();
        $mock_req->expects($this->once())
                 ->method('get_curl_command')
                 ->with('https://ur.l', [])
                 ->will($this->returnValue('curl_command'));
        $requests = [$mock_req];
        $req_property = $this->reflection->getProperty('requests');
        $req_property->setAccessible(TRUE);
        $req_property->setValue($this->class, $requests);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl_command', $return);
    }

    /**
     * Test basic get_hurl_link functions
     */
    public function testGetHurlURLNoKey(): void
    {
        $return = $this->class->get_hurl_link('https://ur.l');

        $this->assertSame('', $return);
    }

    /**
     * Test basic get_hurl_link functions
     */
    public function testGetHurlURLKey(): void
    {
        $mock_req = $this->getMockBuilder('\PHPDraft\Model\HTTPRequest')
                         ->disableOriginalConstructor()
                         ->getMock();
        $mock_req->expects($this->once())
                 ->method('get_hurl_link')
                 ->with('https://ur.l', [])
                 ->will($this->returnValue('https://hurl.it'));
        $requests = [$mock_req];
        $req_property = $this->reflection->getProperty('requests');
        $req_property->setAccessible(TRUE);
        $req_property->setValue($this->class, $requests);

        $return = $this->class->get_hurl_link('https://ur.l');

        $this->assertSame('https://hurl.it', $return);
    }

    /**
     * Test basic get_method functions
     */
    public function testGetMethodNotSet(): void
    {
        $return = $this->class->get_method();

        $this->assertSame('NONE', $return);
    }

    /**
     * Test basic get_method functions
     */
    public function testGetMethodSet(): void
    {
        $mock_req = $this->getMockBuilder('\PHPDraft\Model\HTTPRequest')
                         ->disableOriginalConstructor()
                         ->getMock();

        $mock_req->method = 'TEST';

        $requests = [$mock_req];
        $req_property = $this->reflection->getProperty('requests');
        $req_property->setAccessible(TRUE);
        $req_property->setValue($this->class, $requests);

        $return = $this->class->get_method();

        $this->assertSame('TEST', $return);
    }

    /**
     * Test basic build_url functions
     */
    public function testBuildURLBase(): void
    {
        $parent = $this->getMockBuilder('\PHPDraft\Model\Resource')
                       ->disableOriginalConstructor()
                       ->getMock();

        $parent->method = '/base';

        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $parent);

        $req_property = $this->reflection->getProperty('href');
        $req_property->setAccessible(TRUE);
        $req_property->setValue($this->class, '/url');

        $return = $this->class->build_url();

        $this->assertSame('/url', $return);
    }

    /**
     * Test basic build_url functions
     */
    public function testBuildURLOverlap(): void
    {
        $parent = $this->getMockBuilder('\PHPDraft\Model\Resource')
                       ->disableOriginalConstructor()
                       ->getMock();

        $parent->method = '/url';

        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $parent);

        $req_property = $this->reflection->getProperty('href');
        $req_property->setAccessible(TRUE);
        $req_property->setValue($this->class, '/url/level');

        $return = $this->class->build_url();

        $this->assertSame('/url/level', $return);
    }

    /**
     * Test basic build_url functions
     */
    public function testBuildURLClean(): void
    {
        $parent = $this->getMockBuilder('\PHPDraft\Model\Resource')
                       ->disableOriginalConstructor()
                       ->getMock();

        $parent->method = '/url';

        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $parent);

        $req_property = $this->reflection->getProperty('href');
        $req_property->setAccessible(TRUE);
        $req_property->setValue($this->class, '/url/level');

        $this->mock_function('strip_tags', function() { return "STRIPPED";});

        $return = $this->class->build_url('', TRUE);

        $this->unmock_function('strip_tags');

        $this->assertSame('STRIPPED', $return);
    }

    /**
     * Test basic build_url functions
     */
    public function testBuildURLVars(): void
    {
        $parent = $this->getMockBuilder('\PHPDraft\Model\Resource')
                       ->disableOriginalConstructor()
                       ->getMock();

        $parent->method = '/url';

        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $parent);

        $var1 = $this->getMockBuilder('\PHPDraft\Model\Elements\ObjectStructureElement')
                     ->disableOriginalConstructor()
                     ->getMock();

        $var1->expects($this->once())
             ->method('string_value')
             ->will($this->returnValue('STRING'));
        $var1->key = 'KEY';

        $vars = new \stdClass();
        $vars->value = [ $var1 ];

        $property = $this->reflection->getProperty('url_variables');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $vars);

        $req_property = $this->reflection->getProperty('href');
        $req_property->setAccessible(TRUE);
        $req_property->setValue($this->class, '/url/level');

        $return = $this->class->build_url();

        $this->assertSame('/url/level', $return);
    }
}
