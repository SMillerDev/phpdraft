<?php
/**
 * This file contains the ArrayStructureTest.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements\Tests;


use PHPDraft\Core\TestBase;
use PHPDraft\Model\Elements\ArrayStructureElement;

class ArrayStructureTest extends TestBase
{
    public function setUp()
    {
        $this->class      = new ArrayStructureElement();
        $this->reflection = new \ReflectionClass('PHPDraft\Model\DataStructureElement');
    }

    public function tearDown()
    {
        unset($this->class);
        unset($this->reflection);
    }

//
    /**
    //     * Parse different objects
    //     *
    //     * @dataProvider parseObjectProvider
    //     *
    //     * @param string               $object   JSON Object
    //     * @param ArrayStructureElement $expected Expected Object output
    //     */
//    public function testSuccesfulParse($object, $expected)
//    {
//        $dep = [];
//        $this->class->parse(json_decode($object), $dep);
//        $this->assertSame($this->class->key, $expected->key);
//        $this->assertSame($this->class->value, $expected->value);
//        $this->assertSame($this->class->element, $expected->element);
//        $this->assertSame($this->class->type, $expected->type);
//    }
    /**
     * Provide objects to parse including expected outcome
     *
     * @return array
     */
    public function parseObjectProvider()
    {
        $return         = [];
        $base1          = new ArrayStructureElement();
        $base1->key     = 'Content-Type';
        $base1->value   = 'application/json';
        $base1->element = 'member';
        $base1->type    = 'Struct2';

        $base2          = new ArrayStructureElement();
        $base2->key     = 'Auth2';
        $base2->value   = 'something';
        $base2->element = 'member';
        $base2->type    = 'Struct1';

//        $return[] = [
//            '{"element": "member", "meta": { "description": "Files to be added. Need to be downloaded from the server. Contains relative paths"},
//             "attributes": { "typeAttributes": ["required"]},"content": {"key": {"element": "string","content": "add"},"value": {"element": "array"}}}',
//            $base1,
//        ];
//        $return[] = [
//            '{"element":"member","meta":{"description":"A json array of categories associated with the item"},"attributes":{"typeAttributes":["required"]},
//            "content":{"key":{"element":"string","content":"categoryIds"},"value":{"element":"array","content":[{"element":"string",
//            "content":"\"[\"111\",\"222\",\"333\"]\""}]}}}',
//            $base2,
//        ];

        return $return;
    }
}