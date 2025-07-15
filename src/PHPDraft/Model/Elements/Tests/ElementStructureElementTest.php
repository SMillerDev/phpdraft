<?php

namespace PHPDraft\Model\Elements\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Model\Elements\ElementStructureElement;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class ElementStructureElementTest
 */
#[CoversClass(ElementStructureElement::class)]
class ElementStructureElementTest extends LunrBaseTestCase
{
    private ElementStructureElement $class;

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->class = new ElementStructureElement();
        $this->baseSetUp($this->class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->class);
        unset($this->reflection);
    }

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

    public function testStringValue(): void
    {
        $this->setReflectionPropertyValue('type', 'string');
        $this->setReflectionPropertyValue('description', null);
        $this->assertSame('<li class="list-group-item mdl-list__item"><code>string</code></li>', $this->class->string_value());
    }

    public function testToString(): void
    {
        $this->setReflectionPropertyValue('type', 'string');
        $this->setReflectionPropertyValue('description', null);

        $this->assertSame('<li class="list-group-item mdl-list__item"><code>string</code></li>', $this->class->__toString());
    }

    public function testToStringCustomType(): void
    {
        $this->setReflectionPropertyValue('type', 'Cow');
        $this->setReflectionPropertyValue('description', null);

        $this->assertSame('<li class="list-group-item mdl-list__item"><a class="code" title="Cow" href="#object-cow">Cow</a></li>', $this->class->__toString());
    }

    public function testToStringDescription(): void
    {
        $this->setReflectionPropertyValue('type', 'Cow');
        $this->setReflectionPropertyValue('description', 'Something');

        $this->assertSame('<li class="list-group-item mdl-list__item"><a class="code" title="Cow" href="#object-cow">Cow</a> - <span class="description">Something</span></li>', $this->class->__toString());
    }

    public function testToStringValue(): void
    {
        $this->setReflectionPropertyValue('type', 'Cow');
        $this->setReflectionPropertyValue('value', 'stuff');
        $this->setReflectionPropertyValue('description', null);

        $this->assertSame('<li class="list-group-item mdl-list__item"><a class="code" title="Cow" href="#object-cow">Cow</a> - <span class="example-value pull-right">stuff</span></li>', $this->class->__toString());
    }

    public function testToStringDescriptionAndValue(): void
    {
        $this->setReflectionPropertyValue('type', 'Cow');
        $this->setReflectionPropertyValue('value', 'stuff');
        $this->setReflectionPropertyValue('description', 'Something');

        $this->assertSame('<li class="list-group-item mdl-list__item"><a class="code" title="Cow" href="#object-cow">Cow</a> - <span class="description">Something</span> - <span class="example-value pull-right">stuff</span></li>', $this->class->__toString());
    }
}
