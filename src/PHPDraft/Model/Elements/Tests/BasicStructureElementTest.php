<?php

/**
 * This file contains the BasicStructureElementTest.php
 *
 * @package PHPDraft\Model\Elements
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Model\Elements\ArrayStructureElement;
use PHPDraft\Model\Elements\BasicStructureElement;
use PHPDraft\Model\Elements\ElementStructureElement;
use PHPDraft\Model\Elements\ObjectStructureElement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class BasicStructureElementTest
 */
#[CoversClass(BasicStructureElement::class)]
class BasicStructureElementTest extends LunrBaseTestCase
{
    private BasicStructureElement $class;

    /**
     * Set up tests
     */
    public function setUp(): void
    {
        $this->class      = new class extends BasicStructureElement {
            public function parse(?object $object, array &$dependencies): BasicStructureElement
            {
                return $this;
            }

            public function __toString(): string
            {
                return '';
            }

            protected function new_instance(): BasicStructureElement
            {
                return new self();
            }
        };

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
     * Test if the value the class is initialized with is correct
     *
     * @param mixed  $value        Value to set to the class
     * @param mixed $string_value Expected string representation
     */
    #[DataProvider('stringValueProvider')]
    public function testStringValue(mixed $value, mixed $string_value): void
    {
        $this->setReflectionPropertyValue('value', $value);

        $this->mockFunction('rand', fn() => 0);
        $return = $this->class->string_value();
        $this->unmockFunction('rand');

        $this->assertSame($string_value, $return);
    }

    /**
     * Provide string values
     *
     * @return array
     */
    public static function stringValueProvider(): array
    {
        $return = [];

        $return[] = ['hello', 'hello'];
        $return[] = [1, 1];
        $return[] = [true, true];
        $return[] = [[1], 1];

        $obj        = new ArrayStructureElement();
        $obj->value = 'hello';
        $return[]   = [[$obj], 'hello'];

        return $return;
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testParseCommonDeps(): void
    {
        $dep = [];

        $json = '{"meta":{},"attributes":{},"content":{"key":{"element": "string", "content":"key"}, "value":{"element":"cat"}}}';

        $answer = new ObjectStructureElement();
        $answer->key = new ElementStructureElement();
        $answer->key->type = 'string';
        $answer->key->value = 'key';
        $answer->type = 'cat';
        $answer->description = null;

        $method = $this->getReflectionMethod('parse_common');
        $method->invokeArgs($this->class, [json_decode($json), &$dep]);

        $this->assertEquals($answer->key, $this->class->key);
        $this->assertEquals($answer->type, $this->class->type);
        $this->assertEquals($answer->description, $this->class->description);
        $this->assertEquals($answer->status, $this->class->status);
        $this->assertEquals(['cat'], $dep);
    }

    /**
     * Test if the value the class is initialized with is correct
     *
     * @param mixed                 $value          Value to set to the class
     * @param BasicStructureElement $expected_value Expected string representation
     */
    #[DataProvider('parseValueProvider')]
    public function testParseCommon(mixed $value, BasicStructureElement $expected_value): void
    {
        $dep = [];
        $method = $this->getReflectionMethod('parse_common');
        $method->invokeArgs($this->class, [$value, &$dep]);

        $this->assertEquals($expected_value->key, $this->class->key);
        $this->assertEquals($expected_value->type, $this->class->type);
        $this->assertEquals($expected_value->description, $this->class->description);
        $this->assertEquals($expected_value->status, $this->class->status);
        $this->assertEquals([], $dep);
    }

    public static function parseValueProvider(): array
    {
        $return = [];

        $json = '{"meta":{},"attributes":{},"content":{"key":{"element": "string", "content":"key"}, "value":{"element":"string"}}}';
        $obj = json_decode($json);

        $answer = new ObjectStructureElement();
        $answer->key = new ElementStructureElement();
        $answer->key->type = 'string';
        $answer->key->value = 'key';
        $answer->type = 'string';
        $answer->description = PHP_EOL;

        $return[] = [$obj, $answer];

        $obj2 = clone $obj;
        $obj2->attributes->typeAttributes = [1, 2];
        $answer->status = [1, 2];

        $return[] = [$obj2, $answer];

        $obj3 = clone $obj;
        $obj3->meta->description = '__hello__';
        $answer->description = '__hello__';

        $return[] = [$obj3, $answer];

        return $return;
    }
}
