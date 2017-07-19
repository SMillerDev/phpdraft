<?php
/**
 * This file contains the CategoryTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use PHPDraft\Model\Category;
use PHPDraft\Model\HierarchyElement;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;

/**
 * Class CategoryTest
 * @covers \PHPDraft\Model\Category
 */
class CategoryTest extends HierarchyElementChildTest
{
    /**
     * Set up
     */
    public function setUp()
    {
        parent::setUp();
        $this->class      = new Category();
        $this->reflection = new ReflectionClass('\PHPDraft\Model\Category');
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        unset($this->class);
        unset($this->parent);
        unset($this->reflection);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testStructuresSetup()
    {
        $this->assertSame([], $this->class->structures);
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalled()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $obj = new \stdClass();
        $obj->content = [];

        $this->class->parse($obj);

        $this->assertSame($this->parent, $property->getValue($this->class));
    }

    /**
     * Test basic parse functions where 'element=resource'
     */
    public function testParseIsCalledResource()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $json = '{"content":[{"element":"resource", "content":[{"element":"copy", "content":""}]}]}';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $cproperty = $this->reflection->getProperty('children');
        $cproperty->setAccessible(TRUE);
        $this->assertNotEmpty($cproperty->getValue($this->class));
    }

    /**
     * Test basic parse functions where 'element=dataStructure'
     */
    public function testParseIsCalledObject()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $json = '{"content":[{"element":"dataStructure", "content":{"key":{"content":"none"}, "value":{"element":"none"}}}]}';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $s_property = $this->reflection->getProperty('structures');
        $s_property->setAccessible(TRUE);
        $this->assertNotEmpty($s_property->getValue($this->class));
    }

    /**
     * Test basic parse functions where 'element=dataStructure'
     */
    public function testParseIsCalledObjectMetaID()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $json = '{"content":[{"element":"dataStructure", "content":[{"meta":{"id":4}, "key":{"content":"none"}, "value":{"element":"none"}}]}]}';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $s_property = $this->reflection->getProperty('structures');
        $s_property->setAccessible(TRUE);
        $this->assertNotEmpty($s_property->getValue($this->class));
    }

    /**
     * Test basic parse functions where 'element=henk'
     */
    public function testParseIsCalledDef()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $json = '{"content":[{"element":"henk", "content":[{"element":"copy", "content":""}]}]}';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $property->getValue($this->class));

        $c_property = $this->reflection->getProperty('children');
        $c_property->setAccessible(TRUE);
        $this->assertEmpty($c_property->getValue($this->class));

        $s_property = $this->reflection->getProperty('structures');
        $s_property->setAccessible(TRUE);
        $this->assertEmpty($s_property->getValue($this->class));
    }

    /**
     * Test basic get_href
     */
    public function testGetHrefIsCalledWithParent()
    {
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->parent);

        $this->parent->expects($this->once())
                     ->method('get_href')
                     ->will($this->returnValue('hello'));

        $result = $this->class->get_href();

        $this->assertSame($result, 'hello-');
    }
}