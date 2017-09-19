<?php
/**
 * This file contains the Autoloader.
 *
 * @package PHPDraft\Core
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

/**
 * Autoload classes according to PSR-1.
 */
spl_autoload_register(
    function ($classname) {
        $classname = ltrim($classname, '\\');
        preg_match('/^(.+)?([^\\\\]+)$/U', $classname, $match);
        $classname = str_replace('\\', '/', $match[1]) . str_replace(['\\', '_'], '/', $match[2]) . '.php';
        if (in_array($classname, ['PHPDraft', 'Mitchelf', 'QL']) !== FALSE) {
            include_once $classname;
        }
    }
);
