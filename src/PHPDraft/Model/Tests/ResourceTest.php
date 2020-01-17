<?php
/**
 * This file contains the ResourceTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;


use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\HierarchyElement;
use PHPDraft\Model\Resource;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;

/**
 * Class ResourceTest
 * @covers \PHPDraft\Model\Resource
 */
class ResourceTest extends LunrBaseTest
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
        $this->parent     = $this->getMockBuilder('\PHPDraft\Model\Category')
                                 ->getMock();
        $this->class      = new Resource($this->parent);
        $this->reflection = new ReflectionClass('PHPDraft\Model\Resource');
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
        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalled(): void
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
    public function testParseIsCalledNoHREF(): void
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
    public function testParseIsCalledIsCopy(): void
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
    public function testParseIsCalledIsNotCopy(): void
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
