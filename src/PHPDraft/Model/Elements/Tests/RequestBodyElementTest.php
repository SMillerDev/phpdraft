<?php
/**
 * This file contains the RequestBodyElementTest.php
 *
 * @package PHPDraft\Model\Elements
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\Model\Elements\RequestBodyElement;

/**
 * Class RequestBodyElementTest
 * @covers PHPDraft\Model\Elements\RequestBodyElement
 */
class RequestBodyElementTest extends BaseTest
{

    /**
     * Set up tests
     * @return void
     */
    public function setUp()
    {
        $this->class      = new RequestBodyElement();
        $this->reflection = new \ReflectionClass('PHPDraft\Model\Elements\RequestBodyElement');
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly()
    {
        $property = $this->reflection->getProperty('element');
        $property->setAccessible(TRUE);
        $this->assertNull($property->getValue($this->class));
    }
}