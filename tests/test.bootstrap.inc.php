<?php

/**
 * PHPUnit bootstrap file.
 *
 * Set include path and initialize autoloader.
 *
 * PHP Version 5.6
 *
 * @package    PHPDraft\Core
 * @author     Sean Molenaar <sean@m2mobi.com>
 * @license    https://github.com/SMillerDev/phpdraft/blob/master/LICENSE GPLv3 License
 */

$base = __DIR__ . '/..';

set_include_path(
    $base . '/src:' .
    $base . '/tests:' .
    $base . '/tests/statics:' .
    get_include_path()
);

// Load and setup class file autloader
require_once $base . '/vendor/autoload.php';

define('THIRD_PARTY_ALLOWED', TRUE);

if (defined('TEST_STATICS') === FALSE)
{
    define('TEST_STATICS', __DIR__ . '/statics');
}

?>
