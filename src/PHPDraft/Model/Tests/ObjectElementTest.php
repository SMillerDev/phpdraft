<?php

/**
 * This file contains the ObjectElementTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\Elements\ObjectStructureElement;

/**
 * Class ObjectElementTest
 */
class ObjectElementTest extends LunrBaseTest
{

    private ObjectStructureElement $class;

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->class = new ObjectStructureElement();
        $this->baseSetUp($this->class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->parent);
        parent::tearDown();
    }

    /**
     * @covers \PHPDraft\Model\Elements\ObjectStructureElement
     */
    public function testKeySetup(): void
    {
        $this->assertSame(null, $this->class->key);
    }

    /**
     * @covers \PHPDraft\Model\Elements\ObjectStructureElement
     */
    public function testTypeSetup(): void
    {
        $this->assertSame(null, $this->class->type);
    }

    /**
     * @covers \PHPDraft\Model\Elements\ObjectStructureElement
     */
    public function testDescriptionSetup(): void
    {
        $this->assertSame(null, $this->class->description);
    }

    /**
     * @covers \PHPDraft\Model\Elements\ObjectStructureElement
     */
    public function testElementSetup(): void
    {
        $this->assertSame(null, $this->class->element);
    }

    /**
     * @covers \PHPDraft\Model\Elements\ObjectStructureElement
     */
    public function testValueSetup(): void
    {
        $this->assertSame(null, $this->class->value);
    }

    /**
     * @covers \PHPDraft\Model\Elements\ObjectStructureElement
     */
    public function testStatusSetup(): void
    {
        $this->assertSame([], $this->class->status);
    }

    /**
     * @covers \PHPDraft\Model\Elements\ObjectStructureElement
     */
    public function testDepsSetup(): void
    {
        $this->assertSame(null, $this->class->deps);
    }
}
