<?php

/**
 * This file contains the CategoryTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use PHPDraft\Model\Category;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class CategoryTest
 */
#[CoversClass(Category::class)]
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
        $this->setReflectionPropertyValue('parent', $this->parent);

        $obj = (object) [];
        $obj->content = [];

        $this->class->parse($obj);

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
    }

    /**
     * Test basic parse functions where 'element=resource'
     */
    public function testParseIsCalledResource(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $json = '{"content":[{"element":"resource", "content":[{"element":"copy", "content":""}]}]}';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));

        $this->assertNotEmpty($this->getReflectionPropertyValue('children'));
    }

    /**
     * Test basic parse functions where 'element=dataStructure'
     */
    public function testParseIsCalledObject(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $json = '{"content":[{"element":"dataStructure", "content":{"element": "object", "key":{"content":"none"}, "value":{"element":"none"}}}]}';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertNotEmpty($this->getReflectionPropertyValue('structures'));
    }

    /**
     * Test basic parse functions where 'element=dataStructure'
     */
    public function testParseIsCalledObjectMetaID(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

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

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertNotEmpty($this->getReflectionPropertyValue('structures'));
    }

    /**
     * Test basic parse functions where 'element=henk'
     */
    public function testParseIsCalledDef(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);

        $json = '{"content":[{"element":"henk", "content":[{"element":"copy", "content":""}]}]}';

        $this->class->parse(json_decode($json));

        $this->assertSame($this->parent, $this->getReflectionPropertyValue('parent'));
        $this->assertEmpty($this->getReflectionPropertyValue('children'));
        $this->assertEmpty($this->getReflectionPropertyValue('structures'));
    }

    /**
     * Test basic get_href
     */
    public function testGetHrefIsCalledWithParent(): void
    {
        $this->setReflectionPropertyValue('parent', $this->parent);
        $this->setReflectionPropertyValue('title', 'title');

        $this->parent->expects($this->once())
                     ->method('get_href')
                     ->willReturn('hello');

        $result = $this->class->get_href();

        $this->assertSame($result, 'hello-title');
    }
}
