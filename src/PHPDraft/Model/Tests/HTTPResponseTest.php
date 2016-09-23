<?php
/**
 * This file contains the HTTPResponseTest.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;


use PHPDraft\Core\TestBase;
use PHPDraft\Model\HTTPResponse;
use ReflectionClass;

class HTTPResponseTest extends TestBase
{
    /**
     * Set up
     */
    public function setUp()
    {
        $parent = NULL;
        $this->class      = new HTTPResponse($parent);
        $this->reflection = new ReflectionClass('PHPDraft\Model\HTTPResponse');
    }

    /**
     * Tear down
     */
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
        $property = $this->reflection->getProperty('parent');
        $property->setAccessible(TRUE);
        $this->assertNull($property->getValue($this->class));
    }
}