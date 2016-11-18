<?php
/**
 * This file contains the ArrayStructureTest.php
 *
 * @package PHPDraft\Model\Elements
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\Model\Elements\ArrayStructureElement;

/**
 * Class ArrayStructureTest
 * @covers PHPDraft\Model\Elements\ArrayStructureElement
 */
class ArrayStructureElementTest extends BaseTest
{

    /**
     * Set up tests
     * @return void
     */
    public function setUp()
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
     * @covers       PHPDraft\Model\Elements\ArrayStructureElement::parse
     */
    public function testSuccesfulParse($object, $expected)
    {
        $dep = [];
        $res = $this->class->parse(json_decode($object), $dep);
        $this->assertEquals($res, $expected);
    }

    /**
     * Provide objects to parse including expected outcome
     *
     * @return array
     */
    public function parseObjectProvider()
    {
        $return             = [];
        $base1              = new ArrayStructureElement();
        $base1->key         = 'greet_list';
        $base1->value       = ['hello'];
        $base1->status      = 'required';
        $base1->element     = 'member';
        $base1->type        = 'array';
        $base1->description = "\n";
        $base1->deps        = ['hello'];

        $base2              = new ArrayStructureElement();
        $base2->key         = 'car_id_list';
        $base2->value       = ['Car identifier'];
        $base2->status      = 'optional';
        $base2->element     = 'member';
        $base2->type        = 'array';
        $base2->description = "<p>List of car identifiers to retrieve</p>\n";
        $base2->deps        = ['Car identifier'];

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
                                "element": "hello"
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
                                "element": "Car identifier"
                            }
                        ]
                    }
                }
            }',
            $base2,
        ];

        return $return;
    }

}