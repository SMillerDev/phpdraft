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
    public function setUp()
    {
        $this->parent_transition = $this->createMock('\PHPDraft\Model\Transition');
        $this->parent            = $this->getMockBuilder('\PHPDraft\Model\HierarchyElement')
                                        ->getMock();
        $this->mock_function('microtime', function() {return 1000;});
        $this->class      = new HTTPResponse($this->parent_transition);
        $this->unmock_function('microtime');
        $this->reflection = new ReflectionClass('\PHPDraft\Model\HTTPResponse');
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
        $this->assertSame($this->parent_transition, $this->get_reflection_property_value('parent'));
        $this->assertSame('a9b7ba70783b617e9998dc4dd82eb3c5', $this->get_reflection_property_value('id'));
    }

    /**
     * Tests if get_id returns the correct ID.
     */
    public function testGetId()
    {
        $this->assertSame('a9b7ba70783b617e9998dc4dd82eb3c5', $this->class->get_id());
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalled()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"statusCode":1000, "headers":{"content":[]}}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('statuscode');
        $href_property->setAccessible(TRUE);
        $this->assertSame(1000, $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledExtraHeaders()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"attributes":{"statusCode":1000, "headers":{"content":[{"content":{"key":{"content":"contentKEY"}, "value":{"content":"contentVALUE"}}}]}}, "content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('headers');
        $href_property->setAccessible(TRUE);
        $this->assertSame(['contentKEY'=>'contentVALUE'], $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledWOAttributes()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('statuscode');
        $href_property->setAccessible(TRUE);
        $this->assertNull($href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledCopyContent()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"content":[{"element":"copy", "content":""}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('description');
        $href_property->setAccessible(TRUE);
        $this->assertSame(''.PHP_EOL, $href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledStructContentEmpty()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"content":[{"element":"dataStructure", "content":[]}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('structure');
        $href_property->setAccessible(TRUE);
        $this->assertEmpty($href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledStructContent()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"content":[{"element":"dataStructure", "content":[{}]}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('structure');
        $href_property->setAccessible(TRUE);
        $this->assertNotEmpty($href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledStructContentHasAttr()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"content":[{"content":"hello", "attributes":{"contentType":"content"}, "element":"hello"}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('content');
        $href_property->setAccessible(TRUE);
        $this->assertArrayHasKey('content', $href_property->getValue($this->class));
        $this->assertSame('hello', $href_property->getValue($this->class)['content']);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnStatusCode()
    {
        $property = $this->reflection->getProperty('statuscode');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, 200);

        $obj = '{"statuscode":200, "description":"hello"}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnDesc()
    {
        $property = $this->reflection->getProperty('description');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, 'hello');

        $obj = '{"statuscode":300, "description":"hello"}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertFalse($return);
    }

    /**
     * Test basic is_equal_to functions
     */
    public function testEqualOnBoth()
    {
        $s_property = $this->reflection->getProperty('statuscode');
        $s_property->setAccessible(TRUE);
        $s_property->setValue($this->class, 200);

        $property = $this->reflection->getProperty('description');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, 'hello');

        $obj = '{"statuscode":200, "description":"hello"}';

        $return = $this->class->is_equal_to(json_decode($obj));

        $this->assertTrue($return);
    }


}