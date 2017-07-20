<?php
/**
 * This file contains the ResourceTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;


use PHPDraft\Core\BaseTest;
use PHPDraft\Model\Resource;
use ReflectionClass;

/**
 * Class ResourceTest
 * @covers \PHPDraft\Model\Resource
 */
class ResourceTest extends BaseTest
{
    /**
     * Mock of the parent class
     *
     * @var HierarchyElement|PHPUnit_Framework_MockObject_MockObject
     */
    protected $parent;

    /**
     * Set up
     */
    public function setUp()
    {
        $parent           = NULL;
        $this->parent = $this->getMockBuilder('\PHPDraft\Model\HierarchyElement')
                             ->getMock();
        $this->class      = new Resource($parent);
        $this->reflection = new ReflectionClass('PHPDraft\Model\Resource');
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
    public function testParseIsCalledNoHREF()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"content":[]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertNull($href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsCopy()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = '{"content":[{"element":"copy", "content":""},{"element":"hello", "content":""}, {"element":"hello", "content":""}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $href_property = $this->reflection->getProperty('href');
        $href_property->setAccessible(TRUE);
        $this->assertNull($href_property->getValue($this->class));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalledIsNotCopy()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $child_property = $this->reflection->getProperty('children');
        $child_property->setAccessible(TRUE);
        $this->assertEmpty($child_property->getValue($this->class));

        $obj = '{"content":[{"element":"hello", "content":""}]}';

        $this->class->parse(json_decode($obj));

        $this->assertSame($this->parent, $property->getValue($this->class));
        $this->assertNotEmpty($child_property->getValue($this->class));
    }
}