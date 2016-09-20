<?php
/**
 * This file contains the APIBlueprintElementTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;


use PHPDraft\Core\TestBase;
use PHPDraft\Model\DataStructureElement;
use ReflectionClass;

class DataStructureElementTest extends TestBase
{

    public function setUp()
    {
        $this->class      = new DataStructureElement();
        $this->reflection = new ReflectionClass('PHPDraft\Model\DataStructureElement');
    }

    public function tearDown()
    {
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Parse different objects
     *
     * @dataProvider parseObjectProvider
     *
     * @param string               $object   JSON Object
     * @param DataStructureElement $expected Expected Object output
     */
    public function testSuccesfulParse($object, $expected)
    {
        $dep = [];
        $this->class->parse(json_decode($object), $dep);
        var_dump($this->class);
        $this->assertSame($this->class->key, $expected->key);
        $this->assertSame($this->class->value, $expected->value);
        $this->assertSame($this->class->element, $expected->element);
        $this->assertSame($this->class->type, $expected->type);
    }

    /**
     * Parse different objects and check if the dependencies are saved correctly
     *
     * @dataProvider parseObjectDepProvider
     *
     * @param string $object   JSON Object
     * @param array  $expected Array of expected dependencies
     */
    public function testSuccesfulDependencyCheck($object, $expected)
    {
        $dep = [];
        $this->class->parse(json_decode($object), $dep);
        $this->assertSame($dep, $expected);
    }

    /**
     * Provide objects to parse including expected outcome
     *
     * @return array
     */
    public function parseObjectProvider()
    {
        $return         = [];
        $base1          = new DataStructureElement();
        $base1->key     = 'Content-Type';
        $base1->value   = 'application/json';
        $base1->element = 'member';
        $base1->type    = 'Struct2';

        $base2          = new DataStructureElement();
        $base2->key     = 'Auth2';
        $base2->value   = 'something';
        $base2->element = 'member';
        $base2->type    = 'Struct1';

        $return[] = [
            '{"element":"object","content":[{"element":"member","meta":{"description":"API version with optional client
             architecture identifier"},"content":{"key":{"element":"string"
             ,"content":"version"},"value":{"element":"Struct2","content":"120.a"}}}',
            $base1,
        ];
        $return[] = [
            '{"element": "object","content": {"key": {"element": "string","content": "Auth2"},' .
            '"value": {"element": "Struct1","content": "something"}}}',
            $base2,
        ];

        return $return;
    }

    /**
     * JSON to parse including expected gathered dependency list
     * @return array
     */
    public function parseObjectDepProvider()
    {
        $return   = [];
        $return[] = [
            '{"element":"object","content":[{"element":"member","meta":{"description":"API version"},
            "content":{"key":{"element":"string","content":"version"},"value":{"element":"Struct2","content":"120.a"}}}',
            ['Struct2'],
        ];
        $return[] = [
            '{"element":"member","content":{"key":{"element":"string","content":"flight_list"},
            "value":{"element":"array","content":[{"element":"Flight"}]}}}',
            ['Flight'],
        ];
        $return[] = [
            '{"element": "object",
                "meta": {"description": "Update Data Object"},
                "content": {
                    "key": {"element": "string","content": "data"},
                    "value": { "element": "object",
                        "content": [
                            {   "element": "member",
                                "meta": {"description": "Data that needs to be added to the Struct"},
                                "content": {"key": {"element": "string","content": "add"},
                                    "value": {"element": "Struct1"}
                                }
                            },
                            {   "element": "member",
                                "meta": {"description": "Data that needs to be updated in the Struct"},
                                "content": {"key": {"element": "string","content": "update"},
                                    "value": {"element": "Struct2"}
                                }
                            },
                            {   "element": "member",
                                "meta": {"description": "Data that needs to be deleted from the Struct"},
                                "content": {"key": {"element": "string","content": "delete"},
                                    "value": {"element": "object"}
                                }
                            }
                        ]
                    }
                }
            }', ['Struct1', 'Struct2'],
        ];

        return $return;
    }
}
