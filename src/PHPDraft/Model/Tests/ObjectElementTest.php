<?php

/**
 * This file contains the ObjectElementTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use Lunr\Halo\LunrBaseTest;

/**
 * Class ObjectElementTest
 */
class ObjectElementTest extends LunrBaseTest
{
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
        $this->assertSame(null, $this->class->status);
    }

    /**
     * @covers \PHPDraft\Model\Elements\ObjectStructureElement
     */
    public function testDepsSetup(): void
    {
        $this->assertSame(null, $this->class->deps);
    }
}
