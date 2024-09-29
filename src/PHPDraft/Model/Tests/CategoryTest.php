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
class CategoryTest extends HierarchyElementChildTestBase
{
    private Category $class;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->class = new Category();
        $this->baseSetUp($this->class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->parent);
        parent::tearDown();
    }

    /**
     * Test if the value the class is initialized with is correct
     * @covers \PHPDraft\Model\HierarchyElement
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
        $this->assertPropertySame('parent', null);
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

        $obj = (object) [];
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

        $this->assertNotEmpty($this->get_reflection_property_value('children'));
    }

    /**
     * Test basic parse functions where 'element=dataStructure'
     */
    public function testParseIsCalledObject(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $json = '{"content":[{"element":"dataStructure", "content":{"element": "object", "key":{"content":"none"}, "value":{"element":"none"}}}]}';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertNotEmpty($this->get_reflection_property_value('structures'));
    }

    /**
     * Test basic parse functions where 'element=dataStructure'
     */
    public function testParseIsCalledObjectMetaID(): void
    {
        $this->set_reflection_property_value('parent', $this->parent);

        $json = '{
          "element": "category",
          "meta": {
            "classes": {
              "element": "array",
              "content": [
                {
                  "element": "string",
                  "content": "dataStructures"
                }
              ]
            }
          },
          "content": [
            {
              "element": "dataStructure",
              "content": {
                "element": "object",
                "meta": {
                  "id": {
                    "element": "string",
                    "content": "Org"
                  },
                  "description": {
                    "element": "string",
                    "content": "An organization"
                  }
                },
                "content": [
                  {
                    "element": "member",
                    "content": {
                      "key": {
                        "element": "string",
                        "content": "name"
                      },
                      "value": {
                        "element": "string",
                        "content": "Apiary"
                      }
                    }
                  }
                ]
              }
            }
          ]
        }';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $this->get_reflection_property_value('parent'));
        $this->assertNotEmpty($this->get_reflection_property_value('structures'));
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
                     ->willReturn('hello');

        $result = $this->class->get_href();

        $this->assertSame($result, 'hello-title');
    }
}
