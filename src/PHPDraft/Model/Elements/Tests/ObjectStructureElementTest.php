<?php

/**
 * This file contains the ObjectStructureElementTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\Elements\ArrayStructureElement;
use PHPDraft\Model\Elements\EnumStructureElement;
use PHPDraft\Model\Elements\ObjectStructureElement;
use ReflectionClass;

/**
 * Class ObjectStructureElementTest
 *
 * @covers \PHPDraft\Model\Elements\ObjectStructureElement
 */
class ObjectStructureElementTest extends LunrBaseTest
{

    /**
     * Set up tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class      = new ObjectStructureElement();
        $this->reflection = new ReflectionClass('PHPDraft\Model\Elements\ObjectStructureElement');
    }

    /**
     * Test the setup of new instances
     */
    public function testNewInstance(): void
    {
        $method = $this->reflection->getMethod('new_instance');
        $method->setAccessible(true);
        $return = $method->invoke($this->class);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
    }

    /**
     * Parse different objects
     *
     * @dataProvider parseObjectProvider
     *
     * @param string                 $object   JSON Object
     * @param ObjectStructureElement $expected Expected Object output
     *
     * @covers       \PHPDraft\Model\Elements\ObjectStructureElement::parse
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
    public function parseObjectProvider(): array
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

        $base3              = clone $base2;
        $base3->value       = 'test1 | test2 | test3';

        $base4              = clone $base2;
        $base4->value       = null;

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
                        "attributes": 
                            {"samples":["test1", "test2", "test3"]}
                    }
                }
            }',
            $base3,
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
                        "element": "string"
                    }
                }
            }',
            $base4,
        ];

        return $return;
    }

    /**
     * Test the setup of new instances
     */
    public function testEmptyParse(): void
    {
        $deps = [];
        $return = $this->class->parse(null, $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
        $object = new \stdClass();
        $object->key = 'key';
        $return = $this->class->parse($object, $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
    }

    /**
     * Test the setup of new instances
     */
    public function testArrayContentEnumContentParse(): void
    {
        $deps = [];
        $object = '{"element":"enum","content": [{"content":{"key":{"content":"key"},"value":{"element":"value"}}}]}';

        $return = $this->class->parse(json_decode($object), $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
        foreach ($return->value as $item) {
            $this->assertInstanceOf(EnumStructureElement::class, $item);
        }
    }

    /**
     * Test the setup of new instances
     */
    public function testArrayContentObjectContentParse(): void
    {
        $deps = [];
        $object = '{"element":"object","content": [[]]}';

        $return = $this->class->parse(json_decode($object), $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
        foreach ($return->value as $item) {
            $this->assertInstanceOf(ObjectStructureElement::class, $item);
        }
    }

    /**
     * Test the setup of new instances
     */
    public function testValueStructureEnumContentParse(): void
    {
        $deps = [];
        $object = '{"element":"enum","content": {"key":{"content":"key"},"value":{"element":"enum"}}}';

        $return = $this->class->parse(json_decode($object), $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
        $this->assertInstanceOf(EnumStructureElement::class, $return->value);
    }

    /**
     * Test the setup of new instances
     */
    public function testValueStructureArrayContentParse(): void
    {
        $deps = [];
        $object = '{"element":"enum","content": {"key":{"content":"key"},"value":{"element":"array"}}}';

        $return = $this->class->parse(json_decode($object), $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
        $this->assertInstanceOf(ArrayStructureElement::class, $return->value);
    }

    /**
     * Test the setup of new instances
     */
    public function testValueStructureObjectContentParse(): void
    {
        $deps = [];
        $object = '{"element":"enum","content": {"key":{"content":"key"},"value":{"element":"object"}}}';

        $return = $this->class->parse(json_decode($object), $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
        $this->assertInstanceOf(ObjectStructureElement::class, $return->value);
    }

    /**
     * Test the setup of new instances
     */
    public function testValueStructureObjectContentParseContent(): void
    {
        $deps = [];
        $object = '{"element":"enum","content": {"key":{"content":"key"},"value":{"element":"object", "content":{}}}}';

        $return = $this->class->parse(json_decode($object), $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
        $this->assertInstanceOf(ObjectStructureElement::class, $return->value);
    }

    /**
     * Test the setup of new instances
     */
    public function testToStringBasic(): void
    {
        $return = $this->class->__toString();
        $this->assertSame('<span class="example-value pull-right">{  }</span>', $return);
    }

    /**
     * Test the setup of new instances
     */
    public function testToStringArray(): void
    {
        $this->class->value = ['hello'];
        $return = $this->class->__toString();
        $this->assertSame('<table class="table table-striped mdl-data-table mdl-js-data-table ">hello</table>', $return);

        $this->class->value = [new ArrayStructureElement()];
        $return = $this->class->__toString();
        $this->assertSame('<table class="table table-striped mdl-data-table mdl-js-data-table "><span class="example-value pull-right">[ ]</span></table>', $return);
    }

    /**
     * Test the setup of new instances
     */
    public function testToStringNullValue(): void
    {
        $this->class->key = 'hello';
        $return = $this->class->__toString();
        $this->assertSame('<tr><td><span>hello</span></td><td><a class="code" href="#object-"></a></td><td> <span class="status"></span></td><td></td><td></td></tr>', $return);
    }

    /**
     * Test the setup of new instances
     */
    public function testToStringObjectValue(): void
    {
        $this->class->key = 'hello';
        $this->class->value = new ObjectStructureElement();
        $return = $this->class->__toString();
        $this->assertSame('<tr><td><span>hello</span></td><td><a class="code" href="#object-"></a></td><td> <span class="status"></span></td><td></td><td><div class="sub-struct"><span class="example-value pull-right">{  }</span></div></td></tr>', $return);
    }

    /**
     * Test the setup of new instances
     */
    public function testToStringArrayValue(): void
    {
        $this->class->key = 'hello';
        $this->class->value = new ArrayStructureElement();
        $return = $this->class->__toString();
        $this->assertSame('<tr><td><span>hello</span></td><td><a class="code" href="#object-"></a></td><td> <span class="status"></span></td><td></td><td><div class="array-struct"><span class="example-value pull-right">[ ]</span></div></td></tr>', $return);
    }

    /**
     * Test the setup of new instances
     */
    public function testToStringEnumValue(): void
    {
        $this->class->key = 'hello';
        $this->class->value = new EnumStructureElement();
        $return = $this->class->__toString();
        $this->assertSame('<tr><td><span>hello</span></td><td><a class="code" href="#object-"></a></td><td> <span class="status"></span></td><td></td><td><div class="enum-struct"><span class="example-value pull-right">//list of options</span></div></td></tr>', $return);
    }

    /**
     * Test the setup of new instances
     */
    public function testToStringBoolValue(): void
    {
        $this->class->key = 'hello';
        $this->class->value = true;
        $return = $this->class->__toString();
        $this->assertSame('<tr><td><span>hello</span></td><td><a class="code" href="#object-"></a></td><td> <span class="status"></span></td><td></td><td><span class="example-value pull-right">true</span></td></tr>', $return);
    }

    /**
     * Test the setup of new instances
     */
    public function testToStringOtherValue(): void
    {
        $this->class->key = 'hello';
        $this->class->value = 'world';
        $return = $this->class->__toString();
        $this->assertSame('<tr><td><span>hello</span></td><td><a class="code" href="#object-"></a></td><td> <span class="status"></span></td><td></td><td><span class="example-value pull-right">world</span></td></tr>', $return);
    }

    /**
     * Test the setup of new instances
     */
    public function testToStringOtherValueTypeKnown(): void
    {
        $this->class->type = 'string';
        $this->class->key = 'hello';
        $this->class->value = 'world';
        $return = $this->class->__toString();
        $this->assertSame('<tr><td><span>hello</span></td><td><code>string</code></td><td> <span class="status"></span></td><td></td><td><span class="example-value pull-right">world</span></td></tr>', $return);
    }
}
