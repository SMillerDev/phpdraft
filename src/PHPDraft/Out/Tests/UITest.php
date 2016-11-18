<?php
/**
 * This file contains the UITest.php
 *
 * @package PHPDraft\Out
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\Out\UI;
use ReflectionClass;

/**
 * Class UITest
 *
 * @covers PHPDraft\Out\UI
 */
class UITest extends BaseTest
{

    /**
     * Set up tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->class      = new UI();
        $this->reflection = new ReflectionClass('PHPDraft\Out\UI');
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