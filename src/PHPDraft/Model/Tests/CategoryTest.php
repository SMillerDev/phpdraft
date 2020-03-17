<?php

/**
 * This file contains the CategoryTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use PHPDraft\Model\Category;
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
    public function setUp(): void
    {
        parent::setUp();
        $this->class      = new Category();
        $this->reflection = new ReflectionClass('\PHPDraft\Model\Category');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->class);
        unset($this->parent);
        unset($this->reflection);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testStructuresSetup(): void
    {
        $this->assertSame([], $this->class->structures);
    }

    /**
     * Test basic parse functions
     */
    public function testParseIsCalled(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $obj = new \stdClass();
        $obj->content = [];

        $this->class->parse($obj);

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
    }

    /**
     * Test basic parse functions where 'element=resource'
     */
    public function testParseIsCalledResource(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $json = '{"content":[{"element":"resource", "content":[{"element":"copy", "content":""}]}]}';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));

        $cproperty = $this->reflection->getProperty('children');
        $cproperty->setAccessible(true);
        $this->assertNotEmpty($cproperty->getValue($this->class));
    }

    /**
     * Test basic parse functions where 'element=dataStructure'
     */
    public function testParseIsCalledObject(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $json = '{"content":[{"element":"dataStructure", "content":{"key":{"content":"none"}, "value":{"element":"none"}}}]}';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));

        $s_property = $this->reflection->getProperty('structures');
        $s_property->setAccessible(true);
        $this->assertNotEmpty($s_property->getValue($this->class));
    }

    /**
     * Test basic parse functions where 'element=dataStructure'
     */
    public function testParseIsCalledObjectMetaID(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $json = '{"content":[{"element":"dataStructure", "content":{"meta":{"id":4}, "key":{"content":"none"}, "value":{"element":"none"}}}]}';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));

        $s_property = $this->reflection->getProperty('structures');
        $s_property->setAccessible(true);
        $this->assertNotEmpty($s_property->getValue($this->class));
    }

    /**
     * Test basic parse functions where 'element=henk'
     */
    public function testParseIsCalledDef(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $json = '{"content":[{"element":"henk", "content":[{"element":"copy", "content":""}]}]}';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertEmpty($this->get_reflection_property_value('children'));
        $this->assertEmpty($this->get_reflection_property_value('structures'));
    }

    /**
     * Test basic get_href
     */
    public function testGetHrefIsCalledWithParent(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);
        $this->set_reflection_property_value('title', 'title');

        $this->parent->expects($this->once())
                     ->method('get_href')
                     ->will($this->returnValue('hello'));

        $result = $this->class->get_href();

        $this->assertSame($result, 'hello-title');
    }
}
