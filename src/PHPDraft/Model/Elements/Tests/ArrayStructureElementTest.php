<?php

/**
 * This file contains the ArrayStructureTest.php
 *
 * @package PHPDraft\Model\Elements
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\Elements\ArrayStructureElement;

/**
 * Class ArrayStructureTest
 * @covers \PHPDraft\Model\Elements\ArrayStructureElement
 */
class ArrayStructureElementTest extends LunrBaseTest
{

    /**
     * Set up tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class      = new ArrayStructureElement();
        $this->reflection = new \ReflectionClass('PHPDraft\Model\Elements\ArrayStructureElement');
    }

    /**
     * Parse different objects
     *
     * @dataProvider parseObjectProvider
     *
     * @param string                $object   JSON Object
     * @param ArrayStructureElement $expected Expected Object output
     *
     * @covers \PHPDraft\Model\Elements\ArrayStructureElement::parse
     */
    public function testSuccessfulParse($object, $expected)
    {
        $dep = [];
        $obj = json_decode($object);
        $res = $this->class->parse($obj, $dep);
        $this->assertEquals($expected, $res);
    }

    /**
     * Provide objects to parse including expected outcome
     *
     * @return array
     */
    public function parseObjectProvider(): array
    {
        $return             = [];
        $base1              = new ArrayStructureElement();
        $base1->key         = 'greet_list';
        $base1->value       = [['' => 'string']];
        $base1->status      = 'required';
        $base1->element     = 'member';
        $base1->type        = 'array';
        $base1->is_variable = false;
        $base1->description = "\n";
        $base1->deps        = [];

        $base2              = new ArrayStructureElement();
        $base2->key         = 'car_id_list';
        $base2->value       = [['Truck' => 'Car identifier']];
        $base2->status      = 'optional';
        $base2->element     = 'member';
        $base2->type        = 'array';
        $base2->is_variable = false;
        $base2->description = "<p>List of car identifiers to retrieve</p>\n";
        $base2->deps        = ['Car identifier'];

        $base3              = new ArrayStructureElement();
        $base3->key         = 'car_id_list';
        $base3->value       = [];
        $base3->status      = 'optional';
        $base3->element     = 'member';
        $base3->type        = 'array';
        $base3->is_variable = false;
        $base3->description = "<p>List of car identifiers to retrieve</p>\n";
        $base3->deps        = null;

        $return['generic value type'] = [
            '{
                "element": "member",
                "attributes": {
                    "typeAttributes": [
                        "required"
                    ]
                },
                "content": {
                    "key": {
                        "element": "string",
                        "content": "greet_list"
                    },
                    "value": {
                        "element": "array",
                        "content": [
                            {
                                "element": "string"
                            }
                        ]
                    }
                }
            }',
            $base1,
        ];
        $return['custom value type'] = [
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
                    "key": {
                        "element": "string",
                        "content": "car_id_list"
                    },
                    "value": {
                        "element": "array",
                        "content": [
                            {
                                "element": "Car identifier",
                                "content": "Truck"
                            }
                        ]
                    }
                }
            }',
            $base2,
        ];
        $return[] = [
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
                    "key": {
                        "element": "string",
                        "content": "car_id_list"
                    },
                    "value": {
                        "element": "array"
                    }
                }
            }',
            $base3,
        ];

        return $return;
    }

    /**
     * Test setup of new instances
     */
    public function testNewInstance(): void
    {
        $method = $this->reflection->getMethod('new_instance');
        $method->setAccessible(true);
        $return = $method->invoke($this->class);
        $this->assertInstanceOf(ArrayStructureElement::class, $return);
    }

    /**
     * Test setup of new instances
     */
    public function testToString(): void
    {
        $return = $this->class->__toString();
        $this->assertSame('<span class="example-value pull-right">[ ]</span>', $return);
    }

    /**
     * Test setup of new instances
     */
    public function testToStringWithArray(): void
    {
        $this->class->value = [['string' => 'stuff'], ['int' => 'class']];
        $return = $this->class->__toString();
        $this->assertSame('<ul class="list-group mdl-list"><li class="list-group-item mdl-list__item"><a href="#object-stuff">stuff</a> - <span class="example-value pull-right">string</span></li><li class="list-group-item mdl-list__item"><a href="#object-class">class</a> - <span class="example-value pull-right">int</span></li></ul>', $return);
    }

    /**
     * Test setup of new instances
     */
    public function testToStringWithString(): void
    {
        $this->class->value = 'hello';
        $return = $this->class->__toString();
        $this->assertSame('<span class="example-value pull-right">[ ]</span>', $return);
    }

    /**
     * Test setup of new instances
     */
    public function testToStringWithComplexArray(): void
    {
        $this->class->value = [['type'=>'Bike'], ['stuff'=>'car']];
        $return = $this->class->__toString();
        $this->assertSame('<ul class="list-group mdl-list"><li class="list-group-item mdl-list__item"><a href="#object-bike">Bike</a> - <span class="example-value pull-right">type</span></li><li class="list-group-item mdl-list__item"><a href="#object-car">car</a> - <span class="example-value pull-right">stuff</span></li></ul>', $return);
    }
}
