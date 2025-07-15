<?php

/**
 * This file contains the TransitionTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use PHPDraft\Model\Transition;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class TransitionTest
 * @covers \PHPDraft\Model\Transition
 */
#[CoversClass(Transition::class)]
class TransitionTest extends HierarchyElementChildTestBase
{
    private Transition $class;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->parent     = $this->getMockBuilder('\PHPDraft\Model\Resource')
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $this->class      = new Transition($this->parent);
        $this->baseSetUp($this->class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->parent);
    }


    /**
     * Test if the value the class is initialized with is correct
     */
    public function testChildrenSetup(): void
    {
        $this->assertSame([], $this->class->children);
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

        $obj = '{"attributes":{"href":"something"}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('something', $this->getReflectionPropertyValue('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIssetHrefVariables(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $json = '{"attributes":{"href":"something", "hrefVariables":{"content": [{"element": "member", "hello":"world"}]}}, "content":[]}';
        $obj = json_decode($json);
        $this->class->parse($obj);

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('something', $this->getReflectionPropertyValue('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIssetData(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"element": "dataStructure", "attributes":{"href":"something", "data":{"content":{"hello":"world"}}}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('something', $this->getReflectionPropertyValue('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsNotArrayContent(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"element": "dataStructure", "attributes":{"href":"something", "data":{"content":{"hello":"world"}}}, "content":""}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('something', $this->getReflectionPropertyValue('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsArrayContentNoChild(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"element": "dataStructure", "attributes":{"href":"something", "data":{"content":{"hello":"world"}}}, "content":[{"element":"123", "content":{}}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('something', $this->getReflectionPropertyValue('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsArrayContentWrongChild(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"element": "dataStructure", "attributes":{"href":"something", "data":{"content":{"hello":"world"}}}, "content":[{"element":"123", "content":[{"element":"test"}]}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('something', $this->getReflectionPropertyValue('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsArrayContentRequest(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"element": "dataStructure", "attributes":{"href":"something", "data":{"content":{"hello":"world"}}}, "content":[{"element":"123", "content":[{"element":"httpRequest", "attributes":{"method":"TEST"}, "content":{}}]}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('something', $this->getReflectionPropertyValue('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsArrayContentResponse(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"element": "dataStructure", "attributes":{"href":"something", "data":{"content":{"hello":"world"}}}, "content":[{"element":"123", "content":[{"element":"httpResponse", "content":[], "attributes":{"statusCode":"1000", "headers":{"content":[]}}}]}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('something', $this->getReflectionPropertyValue('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsArrayContentDefault(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = '{"element": "dataStructure", "attributes":{"href":"something", "data":{"content":{"hello":"world"}}}, "content":[{"element":"123", "content":[{"element":"Cow", "content":[], "attributes":{"statusCode":"1000", "headers":{"content":[]}}}]}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('something', $this->getReflectionPropertyValue('href'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsArrayContentRequestList(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $mock_req = $this->getMockBuilder('\PHPDraft\Model\HTTPRequest')
                         ->disableOriginalConstructor()
                         ->getMock();
        $mock_req->expects($this->once())
                 ->method('is_equal_to')
                 ->willReturn(true);

        $requests = [$mock_req];
        $this->setReflectionPropertyValue('requests', $requests);

        $obj = '{"element": "dataStructure", "attributes":{"href":"something", "data":{"content":{"hello":"world"}}}, "content":[{"element":"123", "content":[{"element":"httpRequest", "attributes":{"method":"TEST"}}]}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertSame('something', $this->getReflectionPropertyValue('href'));
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
                 ->willReturn('curl_command');

        $requests = [$mock_req];
        $this->setReflectionPropertyValue('requests', $requests);

        $return = $this->class->get_curl_command('https://ur.l');

        $this->assertSame('curl_command', $return);
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
        $req_property->setAccessible(true);
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

        $parent->href = '/base';

        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(true);
        $property->setValue($this->class, $parent);

        $req_property = $this->reflection->getProperty('href');
        $req_property->setAccessible(true);
        $req_property->setValue($this->class, '/url');

        $return = $this->class->build_url();

        $this->assertSame('/base/url', $return);
    }

    /**
     * Test basic build_url functions
     */
    public function testBuildURLOverlap(): void
    {
        $parent = $this->getMockBuilder('\PHPDraft\Model\Resource')
                       ->disableOriginalConstructor()
                       ->getMock();

        $parent->href = '/url';

        $this->setReflectionPropertyValue('parent', $parent);
        $this->setReflectionPropertyValue('href', '/url/level');

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

        $parent->href = '/url';

        $this->setReflectionPropertyValue('parent', $parent);
        $this->setReflectionPropertyValue('href', '/url/level');

        $this->mockFunction('strip_tags', fn () => "STRIPPED");

        $return = $this->class->build_url('', true);

        $this->unmockFunction('strip_tags');

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

        $parent->href = '/url';

        $var1 = $this->getMockBuilder('\PHPDraft\Model\Elements\ObjectStructureElement')
                     ->disableOriginalConstructor()
                     ->getMock();

        $key1 = $this->getMockBuilder('\PHPDraft\Model\Elements\ElementStructureElement')
                     ->disableOriginalConstructor()
                     ->getMock();

        $key1->value = 'KEY';

        $var1->expects($this->once())
             ->method('string_value')
             ->willReturn('STRING');

        $var1->key = $key1;

        $this->setReflectionPropertyValue('parent', $parent);
        $this->setReflectionPropertyValue('url_variables', [$var1]);
        $this->setReflectionPropertyValue('href', '/url/level');

        $return = $this->class->build_url();

        $this->assertSame('/url/level', $return);
    }
}
