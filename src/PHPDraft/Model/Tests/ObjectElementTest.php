<?php
/**
 * This file contains the ObjectElementTest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use PHPDraft\Core\BaseTest;

/**
 * Class ObjectElementTest
 */
class ObjectElementTest extends BaseTest
{
    public function testKeySetup()
    {
        $this->assertSame(NULL, $this->class->key);
    }

    public function testTypeSetup()
    {
        $this->assertSame(NULL, $this->class->type);
    }

    public function testDescriptionSetup()
    {
        $this->assertSame(NULL, $this->class->description);
    }

    public function testElementSetup()
    {
        $this->assertSame(NULL, $this->class->element);
    }

    public function testValueSetup()
    {
        $this->assertSame(NULL, $this->class->value);
    }

    public function testStatusSetup()
    {
        $this->assertSame('', $this->class->status);
    }

    public function testDepsSetup()
    {
        $this->assertSame(NULL, $this->class->deps);
    }
}