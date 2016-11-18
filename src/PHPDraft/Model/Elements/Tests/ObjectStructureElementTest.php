<?php
/**
 * This file contains the ObjectStructureElementTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\Model\Elements\ObjectStructureElement;
use ReflectionClass;

/**
 * Class ObjectStructureElementTest
 *
 * @covers PHPDraft\Model\Elements\ObjectStructureElement
 */
class ObjectStructureElementTest extends BaseTest
{

    /**
     * Set up tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->class      = new ObjectStructureElement();
        $this->reflection = new ReflectionClass('PHPDraft\Model\Elements\ObjectStructureElement');
    }

    /**
     * Parse different objects
     *
     * @dataProvider parseObjectProvider
     *
     * @param string                 $object   JSON Object
     * @param ObjectStructureElement $expected Expected Object output
     *
     * @covers       PHPDraft\Model\Elements\ObjectStructureElement::parse
     */
    public function testSuccesfulParse($object, $expected)
    {
        $dep = [];
        $res = $this->class->parse(json_decode($object), $dep);
        $this->assertEquals($res, $expected);
        $this->assertSame($res->value, $expected->value);
        $this->assertSame($res->element, $expected->element);
        $this->assertSame($res->type, $expected->type);
    }

    /**
     * Provide objects to parse including expected outcome
     *
     * @return array
     */
    public function parseObjectProvider()
    {
        $return             = [];
        $base1              = new ObjectStructureElement();
        $base1->key         = 'name';
        $base1->value       = 'P10';
        $base1->status      = 'optional';
        $base1->element     = 'member';
        $base1->type        = 'string';
        $base1->description = "<p>desc1</p>\n";

        $base2              = new ObjectStructureElement();
        $base2->key         = 'Auth2';
        $base2->value       = 'something';
        $base2->status      = 'required';
        $base2->element     = 'member';
        $base2->type        = 'string';
        $base2->description = "<p>desc2</p>\n";

        $return[] = [
            '{
                "element": "member",
                "meta": {
                    "description": "desc1"
                },
                "attributes": {
                    "typeAttributes": [ "optional" ]
                },
                "content": {
                    "key": {
                        "element": "string",
                        "content": "name"
                    },
                    "value": {
                        "element": "string",
                        "content": "P10"
                    }
                }
            }',
            $base1,
        ];
        $return[] = [
            '{
                "element": "member",
                "meta": {
                    "description": "desc2"
                },
                "attributes": {
                    "typeAttributes": [ "required" ]
                },
                "content": {
                    "key": {
                        "element": "string",
                        "content": "Auth2"
                    },
                    "value": {
                        "element": "string",
                        "content": "something"
                    }
                }
            }',
            $base2,
        ];

        return $return;
    }

}
