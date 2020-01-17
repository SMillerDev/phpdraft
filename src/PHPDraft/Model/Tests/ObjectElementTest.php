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
        $this->assertSame(NULL, $this->class->key);
    }

    public function testTypeSetup(): void
    {
        $this->assertSame(NULL, $this->class->type);
    }

    public function testDescriptionSetup(): void
    {
        $this->assertSame(NULL, $this->class->description);
    }

    public function testElementSetup(): void
    {
        $this->assertSame(NULL, $this->class->element);
    }

    public function testValueSetup(): void
    {
        $this->assertSame(NULL, $this->class->value);
    }

    public function testStatusSetup(): void
    {
        $this->assertSame('', $this->class->status);
    }

    public function testDepsSetup(): void
    {
        $this->assertSame(NULL, $this->class->deps);
    }
}
