<?php
/**
 * This file contains the UITest.php
 *
 * @package php-drafter\Out
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out\Tests;


use PHPDraft\Core\TestBase;
use PHPDraft\Out\UI;
use ReflectionClass;

class UITest extends TestBase
{
    public function setUp()
    {
        $this->class      = new UI();
        $this->reflection = new ReflectionClass('PHPDraft\Out\UI');
    }

    public function tearDown()
    {
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly()
    {
        $property = $this->reflection->getProperty('versionStringPrinted');
        $property->setAccessible(TRUE);
        $this->assertNull($property->getValue($this->class));
    }
}