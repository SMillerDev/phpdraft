<?php

namespace PHPDraft\Model\Elements\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\Elements\ElementStructureElement;
use ReflectionClass;

/**
 * Class ElementStructureElementTest
 * @covers  \PHPDraft\Model\Elements\ElementStructureElement
 */
class ElementStructureElementTest extends LunrBaseTest
{
    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->class      = new ElementStructureElement();
        $this->reflection = new ReflectionClass('PHPDraft\Model\Elements\ElementStructureElement');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * @covers  \PHPDraft\Model\Elements\ElementStructureElement::parse
     */
    public function testParse(): void
    {
        $json = '{"element": "Cow", "content": "stuff", "meta": {"description": {"content": "desc"}}}';
        $dep  = [];
        $this->class->parse(json_decode($json), $dep);

        $this->assertPropertySame('type', 'Cow');
        $this->assertPropertySame('value', 'stuff');
        $this->assertPropertySame('description', 'desc');
        $this->assertSame(['Cow'], $dep);
    }

    /**
     * @covers  \PHPDraft\Model\Elements\ElementStructureElement::string_value
     */
    public function testStringValue(): void
    {
        $this->set_reflection_property_value('type', 'string');
        $this->set_reflection_property_value('description', null);
        $this->assertSame('<li class="list-group-item mdl-list__item"><code>string</code></li>', $this->class->string_value());
    }

    /**
     * @covers  \PHPDraft\Model\Elements\ElementStructureElement::__toString
     */
    public function testToString(): void
    {
        $this->set_reflection_property_value('type', 'string');
        $this->set_reflection_property_value('description', null);

        $this->assertSame('<li class="list-group-item mdl-list__item"><code>string</code></li>', $this->class->__toString());
    }

    /**
     * @covers  \PHPDraft\Model\Elements\ElementStructureElement::__toString
     */
    public function testToStringCustomType(): void
    {
        $this->set_reflection_property_value('type', 'Cow');
        $this->set_reflection_property_value('description', null);

        $this->assertSame('<li class="list-group-item mdl-list__item"><a class="code" title="Cow" href="#object-cow">Cow</a></li>', $this->class->__toString());
    }

    /**
     * @covers  \PHPDraft\Model\Elements\ElementStructureElement::__toString
     */
    public function testToStringDescription(): void
    {
        $this->set_reflection_property_value('type', 'Cow');
        $this->set_reflection_property_value('description', 'Something');

        $this->assertSame('<li class="list-group-item mdl-list__item"><a class="code" title="Cow" href="#object-cow">Cow</a> - <span class="description">Something</span></li>', $this->class->__toString());
    }

    /**
     * @covers  \PHPDraft\Model\Elements\ElementStructureElement::__toString
     */
    public function testToStringValue(): void
    {
        $this->set_reflection_property_value('type', 'Cow');
        $this->set_reflection_property_value('value', 'stuff');
        $this->set_reflection_property_value('description', null);

        $this->assertSame('<li class="list-group-item mdl-list__item"><a class="code" title="Cow" href="#object-cow">Cow</a> - <span class="example-value pull-right">stuff</span></li>', $this->class->__toString());
    }

    /**
     * @covers  \PHPDraft\Model\Elements\ElementStructureElement::__toString
     */
    public function testToStringDescriptionAndValue(): void
    {
        $this->set_reflection_property_value('type', 'Cow');
        $this->set_reflection_property_value('value', 'stuff');
        $this->set_reflection_property_value('description', 'Something');

        $this->assertSame('<li class="list-group-item mdl-list__item"><a class="code" title="Cow" href="#object-cow">Cow</a> - <span class="description">Something</span> - <span class="example-value pull-right">stuff</span></li>', $this->class->__toString());
    }
}
