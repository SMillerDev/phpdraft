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
     *
     * @var mixed
     */
    protected $class;

    /**
     * Test reflection
     *
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * Clear up tests
     *
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
     * @param string $name   function name
     * @param string $return return to use
     *
     * @return void
     */
    protected function mock_function($name, $return)
    {
        if (extension_loaded('runkit') === true) {
            $this->__runkit_mock_function($name, $return);

            return;
        }

        if (extension_loaded('uopz') === true) {
            $this->__uopz_mock_function($name, $return);

            return;
        }
    }

    /**
     * Mock an internal function with runkit
     *
     * @param string $name   function name
     * @param string $return function to replace it with
     *
     * @return void
     */
    private function __runkit_mock_function($name, $return)
    {
        if (function_exists($name . self::FUNCTION_ID) === false) {
            runkit_function_copy($name, $name . self::FUNCTION_ID);
        }

        runkit_function_redefine($name, '', 'return ' . (is_string($return) ? ('"' . $return . '"') : $return) . ';');
    }

    /**
     * Mock an internal function with uopz
     *
     * @param string $name   function name
     * @param string $return value to return
     *
     * @return void
     */
    private function __uopz_mock_function($name, $return)
    {
        if (PHP_MAJOR_VERSION < 7) {
            \uopz_backup($name);
            \uopz_function($name, function () {
                global $return;

                return $return;
            });

            return;
        }
        \uopz_set_return($name, $return);
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
        if (extension_loaded('runkit') === true) {
            $this->__runkit_unmock_function($name);

            return;
        }

        if (extension_loaded('uopz') === true) {
            $this->__uopz_unmock_function($name);

            return;
        }

    }

    /**
     * Unmock a PHP function from runkit.
     *
     * @param String $name Function name
     *
     * @return void
     */
    private function __runkit_unmock_function($name)
    {
        runkit_function_remove($name);
        runkit_function_rename($name . self::FUNCTION_ID, $name);
    }

    /**
     * Unmock a PHP function from uopz.
     *
     * @param String $name Function name
     *
     * @return void
     */
    private function __uopz_unmock_function($name)
    {
        if (PHP_MAJOR_VERSION < 7) {
            \uopz_restore($name);

            return;
        }

        uopz_unset_return($name);
    }
}