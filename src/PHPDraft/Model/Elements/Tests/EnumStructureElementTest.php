<?php

/**
 * This file contains the EnumStructureElementTest.php
 *
 * @package PHPDraft\Model\Elements
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Model\Elements\ElementStructureElement;
use PHPDraft\Model\Elements\EnumStructureElement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class EnumStructureElementTest
 */
#[CoversClass(EnumStructureElement::class)]
class EnumStructureElementTest extends LunrBaseTestCase
{
    private EnumStructureElement $class;

    /**
     * Set up tests
     * @return void
     */
    public function setUp(): void
    {
        $this->class = new EnumStructureElement();
        $this->baseSetUp($this->class);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly(): void
    {
        $this->assertPropertyEquals('element', null);
    }

    /**
     * Test setup of new instances
     */
    public function testNewInstance(): void
    {
        $method = $this->getReflectionMethod('new_instance');
        $return = $method->invoke($this->class);
        $this->assertInstanceOf(EnumStructureElement::class, $return);
    }

    /**
     * Test setup of new instances
     */
    public function testToStringWithArray(): void
    {
        $this->setReflectionPropertyValue('description', null);

        $value1 = new ElementStructureElement();
        $value1->value = 'hello';
        $value1->type  = 'string';
        $value1->description = null;
        $value2 = new ElementStructureElement();
        $value2->value = 'test';
        $value2->type  = 'int';
        $value2->description = null;
        $this->class->value = [$value1, $value2];
        $return = $this->class->__toString();
        $this->assertSame('<ul class="list-group mdl-list"><li class="list-group-item mdl-list__item"><code>string</code> - <span class="example-value pull-right">hello</span></li><li class="list-group-item mdl-list__item"><a class="code" title="int" href="#object-int">int</a> - <span class="example-value pull-right">test</span></li></ul>', $return);
    }

    /**
     * Test setup of new instances
     */
    public function testToStringWithString(): void
    {
        $this->class->value = 'hello';
        $this->class->key = new ElementStructureElement();
        $this->class->key->type = 'string';
        $this->class->key->value = 'key';
        $this->class->key->description = null;
        $this->class->element = 'string';
        $return = $this->class->__toString();
        $this->assertSame('<tr><td>key</td><td><code>string</code></td><td></td></tr>', $return);
    }

    /**
     * Test setup of new instances
     */
    public function testToStringWithStringComplex(): void
    {
        $this->class->value = 'hello';
        $this->class->key = new ElementStructureElement();
        $this->class->key->type = 'string';
        $this->class->key->value = 'key';
        $this->class->element = 'Car';
        $return = $this->class->__toString();
        $this->assertSame('<tr><td>key</td><td><a class="code" title="Car" href="#object-car">Car</a></td><td></td></tr>', $return);
    }

    /**
     * Test setup of new instances
     */
    public function testToStringWithComplexArray(): void
    {
        $this->setReflectionPropertyValue('description', null);

        $value1 = new ElementStructureElement();
        $value1->value = 'hello';
        $value1->type  = 'bike';
        $value1->description = null;
        $value2 = new ElementStructureElement();
        $value2->value = 'test';
        $value2->type  = 'Car';
        $value2->description = null;
        $this->class->value = [$value1, $value2];
        $return = $this->class->__toString();
        $this->assertSame('<ul class="list-group mdl-list"><li class="list-group-item mdl-list__item"><a class="code" title="bike" href="#object-bike">bike</a> - <span class="example-value pull-right">hello</span></li><li class="list-group-item mdl-list__item"><a class="code" title="Car" href="#object-car">Car</a> - <span class="example-value pull-right">test</span></li></ul>', $return);
    }

    /**
     * Parse different objects
     *
     * @param string               $object   JSON Object
     * @param EnumStructureElement $expected Expected Object output
     */
    #[DataProvider('parseObjectProvider')]
    public function testSuccessfulParse(string $object, EnumStructureElement $expected): void
    {
        $dep = [];
        $res = $this->class->parse(json_decode($object), $dep);
        $this->assertEquals($expected, $res);
    }

    /**
     * Provide objects to parse including expected outcome
     *
     * @return array
     */
    public static function parseObjectProvider(): array
    {
        $value1 = new ElementStructureElement();
        $value1->value = 'item';
        $value1->type  = 'string';
        $value2 = new ElementStructureElement();
        $value2->value = 'another item';
        $value2->type  = 'string';

        $return             = [];
        $base1              = new EnumStructureElement();
        $base1->key         = null;
        $base1->value       = [ $value1, $value2 ];
        $base1->status      = [];
        $base1->element     = 'enum';
        $base1->type        = 'Some simple enum';
        $base1->is_variable = false;
        $base1->description = null;
        $base1->ref = null;
        $base1->deps        = ['Some simple enum'];

        $base2              = new EnumStructureElement();
        $base2->key         = new ElementStructureElement();
        $base2->key->type   = 'string';
        $base2->key->value  = 'car_id_list';
        $base2->value       = 'world';
        $base2->status      = [];
        $base2->element     = 'enum';
        $base2->type        = 'string';
        $base2->description = null;
        $base2->ref = null;
        $base2->is_variable = false;
        $base2->deps        = [];

        $base3              = new EnumStructureElement();
        $base3->key         = new ElementStructureElement();
        $base3->key->type   = 'number';
        $base3->key->value  = '5';
        $base3->value       = '5';
        $base3->status      = ['optional'];
        $base3->element     = 'member';
        $base3->type        = 'number';
        $base3->description = "List of car identifiers to retrieve";
        $base3->ref = null;
        $base3->is_variable = false;
        $base3->deps        = [];

        $base4              = new EnumStructureElement();
        $base4->key         = new ElementStructureElement();
        $base4->value       = [ $value1 ];
        $base4->key->type   = 'string';
        $base4->key->value  = 'item';
        $base4->status      = [];
        $base4->element     = 'enum';
        $base4->type        = 'Some simple enum';
        $base4->is_variable = false;
        $base4->description = null;
        $base4->ref = null;
        $base4->deps        = ['Some simple enum'];

        $return['base enum'] = [
            '{
                "element":"enum",
                "meta":{
                    "id":{
                        "element":"string",
                        "content":"Some simple enum"
                    }
                },
                "attributes":{
                    "enumerations":{
                        "element":"array",
                        "content":[
                            {
                                "element":"string",
                                "attributes":{
                                    "typeAttributes":{
                                        "element":"array",
                                        "content":[
                                            {
                                                "element":"string",
                                                "content":"fixed"
                                            }
                                        ]
                                    }
                                },
                                "content":"item"
                            },
                            {
                                "element":"string",
                                "attributes":{
                                    "typeAttributes":{
                                        "element":"array","content":[
                                            {
                                                "element":"string",
                                                "content":"fixed"
                                            }
                                        ]
                                    }
                                },
                                "content":"another item"
                            }
                        ]
                    }
                }
            }',
            $base1,
        ];
        $return['enum with default'] = [
            '{
              "element": "enum",
              "attributes": {
                "default": {
                  "element": "enum",
                  "content": {
                    "element": "string",
                    "content": "world"
                  }
                },
                "enumerations": {
                  "element": "array",
                  "content": [
                    {
                      "element": "string",
                      "content": "hello"
                    },
                    {
                      "element": "string",
                      "content": "world"
                    },
                    {
                      "element": "string",
                      "content": "tests"
                    }
                  ]
                }
              },
              "content": {
                "element": "string",
                "content": "car_id_list"
              }
            }',
            $base2,
        ];
        $return['basic enum'] = [
            '{
                "element": "member",
                "meta": {
                    "description": "List of car identifiers to retrieve"
                },
                "attributes": {
                    "typeAttributes": [
                        "optional"
                    ]
                },
                "content": {
                    "element": "number",
                    "content": "5"
                }
            }',
            $base3,
        ];
        $return['enum with single enumeration and content'] = [
            '{
                "element":"enum",
                "meta":{
                    "id":{
                        "element":"string",
                        "content":"Some simple enum"
                    }
                },
                "attributes":{
                    "enumerations":{
                        "element":"array",
                        "content":[
                            {
                                "element":"string",
                                "attributes":{
                                    "typeAttributes":{
                                        "element":"array",
                                        "content":[
                                            {
                                                "element":"string",
                                                "content":"fixed"
                                            }
                                        ]
                                    }
                                },
                                "content":"item"
                            }
                        ]
                    }
                },
                "content": {
                    "element": "string",
                    "content": "item"
                }
            }',
            $base4,
        ];

        return $return;
    }
}
