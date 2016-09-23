<?php
/**
 * This file contains the TestBase.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Core;


use PHPUnit_Framework_TestCase;
use ReflectionClass;

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