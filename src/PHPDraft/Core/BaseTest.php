<?php
/**
 * This file contains the BaseTest.php
 *
 * @package PHPDraft\Core
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Core;

use PHPUnit_Framework_TestCase;
use ReflectionClass;

/**
 * Class BaseTest
 */
class BaseTest extends PHPUnit_Framework_TestCase
{
    const FUNCTION_ID = '_phpdraftbu';
    /**
     * Test Class
     * @var mixed
     */
    protected $class;

    /**
     * Test reflection
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * Clear up tests
     * @return void
     */
    public function tearDown()
    {
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Mock an internal function
     *
     * @param string $name function name
     * @param string $mock function to replace it with
     *
     * @return void
     */
    protected function mock_function($name, $mock)
    {
        if (function_exists($name . self::FUNCTION_ID) === FALSE)
        {
            runkit_function_copy($name, $name . self::FUNCTION_ID);
        }

        runkit_function_redefine($name, '', $mock);
    }

    /**
     * Unmock a PHP function.
     *
     * @param String $name Function name
     *
     * @return void
     */
    protected function unmock_function($name)
    {
        runkit_function_remove($name);
        runkit_function_rename($name . self::FUNCTION_ID, $name);
    }
}