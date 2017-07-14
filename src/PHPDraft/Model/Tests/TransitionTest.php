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
    public function setUp()
    {
        parent::setUp();

        $parent           = NULL;
        $this->class      = new Transition($parent);
        $this->reflection = new ReflectionClass('PHPDraft\Model\Transition');
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        unset($this->class);
        unset($this->reflection);
        unset($this->parent);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $this->assertNull($property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalled()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"href":"something"}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertSame('something', $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIssetHrefVariables()
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
    public function testParseIsCalledIssetData()
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
    public function testParseIsCalledIsNotArrayContent()
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
    public function testParseIsCalledIsArrayContentNoChild()
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
    public function testParseIsCalledIsArrayContentWrongChild()
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
    public function testParseIsCalledIsArrayContentRequest()
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
    public function testParseIsCalledIsArrayContentResponse()
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
    public function testParseIsCalledIsArrayContentRequestList()
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
    public function testGetCurlCommandNoKey()
    {
        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('', $return);
    }

    /**
     * Test basic get_curl_command functions
     */
    public function testGetCurlCommandKey()
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
     * Test basic get_method functions
     */
    public function testGetMethodNotSet()
    {
        $return = $this->class->get_method();

        $this->assertSame('NONE', $return);
    }

    /**
     * Test basic get_method functions
     */
    public function testGetMethodSet()
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
    public function testBuildURLBase()
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
    public function testBuildURLOverlap()
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
    public function testBuildURLClean()
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

        $this->mock_function('strip_tags', "STRIPPED");

        $return = $this->class->build_url('', TRUE);

        $this->unmock_function('strip_tags');

        $this->assertSame('STRIPPED', $return);
    }

    /**
     * Test basic build_url functions
     */
    public function testBuildURLVars()
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