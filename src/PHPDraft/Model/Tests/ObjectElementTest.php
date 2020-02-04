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
    public function testKeySetup(): void
    {
        $this->assertSame(null, $this->class->key);
    }

    public function testTypeSetup(): void
    {
        $this->assertSame(null, $this->class->type);
    }

    public function testDescriptionSetup(): void
    {
        $this->assertSame(null, $this->class->description);
    }

    public function testElementSetup(): void
    {
        $this->assertSame(null, $this->class->element);
    }

    public function testValueSetup(): void
    {
        $this->assertSame(null, $this->class->value);
    }

    public function testStatusSetup(): void
    {
        $this->assertSame('', $this->class->status);
    }

    public function testDepsSetup(): void
    {
        $this->assertSame(null, $this->class->deps);
    }
}
