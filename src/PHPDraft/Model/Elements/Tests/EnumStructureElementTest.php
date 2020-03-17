<?php

/**
 * This file contains the EnumStructureElementTest.php
 *
 * @package PHPDraft\Model\Elements
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\Elements\EnumStructureElement;

/**
 * Class EnumStructureElementTest
 * @covers \PHPDraft\Model\Elements\EnumStructureElement
 */
class EnumStructureElementTest extends LunrBaseTest
{
    /**
     * Set up tests
     * @return void
     */
    public function setUp(): void
    {
        $this->class      = new EnumStructureElement();
        $this->reflection = new \ReflectionClass('PHPDraft\Model\Elements\EnumStructureElement');
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly(): void
    {
        $property = $this->reflection->getProperty('element');
        $property->setAccessible(true);
        $this->assertNull($property->getValue($this->class));
    }

    /**
     * Test setup of new instances
     */
    public function testNewInstance(): void
    {
        $method = $this->reflection->getMethod('new_instance');
        $method->setAccessible(true);
        $return = $method->invoke($this->class);
        $this->assertInstanceOf(EnumStructureElement::class, $return);
    }

    /**
     * Test setup of new instances
     */
    public function testToString(): void
    {
        $return = $this->class->__toString();
        $this->assertSame('<span class="example-value pull-right">//list of options</span>', $return);
    }

    /**
     * Test setup of new instances
     */
    public function testToStringWithArray(): void
    {
        $this->class->value = ['hello' => 'string', 'test' => 'int'];
        $return = $this->class->__toString();
        $this->assertSame('<ul class="list-group mdl-list"><li class="list-group-item mdl-list__item"><code>string</code> - <span class="example-value pull-right">hello</span></li><li class="list-group-item mdl-list__item"><a href="#object-int">int</a> - <span class="example-value pull-right">test</span></li></ul>', $return);
    }

    /**
     * Test setup of new instances
     */
    public function testToStringWithString(): void
    {
        $this->class->value = 'hello';
        $this->class->key = 'key';
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
        $this->class->key = 'key';
        $this->class->element = 'Car';
        $return = $this->class->__toString();
        $this->assertSame('<tr><td>key</td><td><code><a href="#object-car">Car</a></code></td><td></td></tr>', $return);
    }

    /**
     * Test setup of new instances
     */
    public function testToStringWithComplexArray(): void
    {
        $this->class->value = ['hello' => 'bike', 'test' => 'Car'];
        $return = $this->class->__toString();
        $this->assertSame('<ul class="list-group mdl-list"><li class="list-group-item mdl-list__item"><a href="#object-bike">bike</a> - <span class="example-value pull-right">hello</span></li><li class="list-group-item mdl-list__item"><a href="#object-car">Car</a> - <span class="example-value pull-right">test</span></li></ul>', $return);
    }

    /**
     * Parse different objects
     *
     * @dataProvider parseObjectProvider
     *
     * @param string               $object   JSON Object
     * @param EnumStructureElement $expected Expected Object output
     *
     * @covers       \PHPDraft\Model\Elements\EnumStructureElement::parse
     */
    public function testSuccesfulParse($object, $expected)
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
    public function parseObjectProvider(): array
    {
        $return             = [];
        $base1              = new EnumStructureElement();
        $base1->key         = 'greet_list';
        $base1->value       = ['world' => 'hello'];
        $base1->status      = 'required';
        $base1->element     = 'member';
        $base1->type        = 'array';
        $base1->description = "\n";
        $base1->deps        = ['hello'];

        $base2              = new EnumStructureElement();
        $base2->key         = 'car_id_list';
        $base2->value       = ['world' => 'Car identifier'];
        $base2->status      = 'optional';
        $base2->element     = 'member';
        $base2->type        = 'array';
        $base2->description = "<p>List of car identifiers to retrieve</p>\n";
        $base2->deps        = ['Car identifier'];

        $base3              = new EnumStructureElement();
        $base3->key         = 'car_id_list';
        $base3->value       = 'car_id_list';
        $base3->status      = 'optional';
        $base3->element     = 'member';
        $base3->type        = 'array';
        $base3->description = "<p>List of car identifiers to retrieve</p>\n";
        $base3->deps        = null;

        $return[] = [
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
                                "element": "hello",
                                "content": "world"
                            }
                        ]
                    }
                }
            }',
            $base1,
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
                        "element": "array",
                        "content": [
                            {
                                "element": "Car identifier",
                                "content": "world"
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
}
