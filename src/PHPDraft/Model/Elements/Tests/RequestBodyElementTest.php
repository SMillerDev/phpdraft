<?php
/**
 * This file contains the RequestBodyElementTest.php
 *
 * @package PHPDraft\Model\Elements
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\Elements\ArrayStructureElement;
use PHPDraft\Model\Elements\EnumStructureElement;
use PHPDraft\Model\Elements\ObjectStructureElement;
use PHPDraft\Model\Elements\RequestBodyElement;

/**
 * Class RequestBodyElementTest
 * @covers \PHPDraft\Model\Elements\RequestBodyElement
 */
class RequestBodyElementTest extends LunrBaseTest
{

    /**
     * Set up tests
     * @return void
     */
    public function setUp()
    {
        $this->class      = new RequestBodyElement();
        $this->reflection = new \ReflectionClass('PHPDraft\Model\Elements\RequestBodyElement');
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly()
    {
        $property = $this->reflection->getProperty('element');
        $property->setAccessible(TRUE);
        $this->assertNull($property->getValue($this->class));
    }

    /**
     * Test setup of new instances
     */
    public function testNewInstance()
    {
        $method = $this->reflection->getMethod('new_instance');
        $method->setAccessible(TRUE);
        $return = $method->invoke($this->class);
        $this->assertInstanceOf(RequestBodyElement::class, $return);
    }

    /**
     * Test setup of new instances
     */
    public function testPrintBasic()
    {
        $this->class->key = 'key';
        $return = $this->class->print_request();
        $this->assertSame('key=<span>?</span>', $return);
    }

    /**
     * Test setup of new instances
     */
    public function testPrintBasicArray()
    {
        $this->class->key = 'key';
        $this->class->value = 'value';
        $c1 = clone $this->class;
        $c2 = clone $this->class;
        $this->class->value = [ $c1, $c2 ];
        $return = $this->class->print_request();
        $this->assertSame('<code class="request-body">key=<span>value</span>&key=<span>value</span></code>', $return);
    }

    /**
     * Test setup of new instances
     */
    public function testPrintJson()
    {
        $this->class->key = 'key';
        $return = $this->class->print_request('application/json');
        $this->assertSame('{"key":"?"}', $return);
    }

    /**
     * Test setup of new instances
     */
    public function testPrintJsonArray()
    {
        $this->class->key = 'key';
        $this->class->value = 'value';
        $c1 = clone $this->class;
        $c2 = clone $this->class;
        $this->class->value = [ $c1, $c2 ];
        $return = $this->class->print_request('application/json');
        $this->assertSame("<code class=\"request-body\">{\"key\":\"value\"}\n{\"key\":\"value\"}</code>", $return);
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
    public function parseObjectProvider()
    {
        $return             = [];
        $base1              = new RequestBodyElement();
        $base1->key         = 'name';
        $base1->value       = 'P10';
        $base1->status      = 'optional';
        $base1->element     = 'member';
        $base1->type        = 'string';
        $base1->description = "<p>desc1</p>\n";

        $base2              = new RequestBodyElement();
        $base2->key         = 'Auth2';
        $base2->value       = 'something';
        $base2->status      = 'required';
        $base2->element     = 'member';
        $base2->type        = 'string';
        $base2->description = "<p>desc2</p>\n";

        $base3              = clone $base2;
        $base3->value       = 'test1 | test2 | test3';

        $base4              = clone $base2;
        $base4->value       = NULL;

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
    public function testEmptyParse()
    {
        $deps = [];
        $return = $this->class->parse(NULL, $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
        $object = new \stdClass();
        $object->key = 'key';
        $return = $this->class->parse($object, $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
    }

    /**
     * Test the setup of new instances
     */
    public function testArrayContentEnumContentParse()
    {
        $deps = [];
        $object = '{"element":"enum","content": [{"content":{"key":{"content":"key"},"value":{"element":"value"}}}]}';

        $return = $this->class->parse(json_decode($object), $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
        foreach ($return->value as $item)
        {
            $this->assertInstanceOf(EnumStructureElement::class, $item);
        }
    }

    /**
     * Test the setup of new instances
     */
    public function testArrayContentObjectContentParse()
    {
        $deps = [];
        $object = '{"element":"object","content": [[]]}';

        $return = $this->class->parse(json_decode($object), $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
        foreach ($return->value as $item)
        {
            $this->assertInstanceOf(ObjectStructureElement::class, $item);
        }
    }

    /**
     * Test the setup of new instances
     */
    public function testValueStructureEnumContentParse()
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
    public function testValueStructureArrayContentParse()
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
    public function testValueStructureObjectContentParse()
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
    public function testValueStructureObjectContentParseContent()
    {
        $deps = [];
        $object = '{"element":"enum","content": {"key":{"content":"key"},"value":{"element":"object", "content":{}}}}';

        $return = $this->class->parse(json_decode($object), $deps);
        $this->assertInstanceOf(ObjectStructureElement::class, $return);
        $this->assertInstanceOf(ObjectStructureElement::class, $return->value);
    }
}