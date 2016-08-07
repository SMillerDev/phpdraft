<?php
/**
 * This file contains the create_phar.php
 *
 * @package php-drafter\SOMETHING
 * @author Sean Molenaar<sean@seanmolenaar.eu>
 */
$phar_file = 'php-drafter.phar';
$shebang = '/usr/bin/php';
//$shebang = system('/usr/bin/env php');

if (file_exists($phar_file)) {
    unlink($phar_file);
}
$phar = new Phar($phar_file);

$phar = $phar->convertToExecutable(Phar::PHAR);

// start buffering. Mandatory to modify stub.
$phar->startBuffering();

// Get the default stub. You can create your own if you have specific needs
$defaultStub = $phar->createDefaultStub('index.php');

// Adding files
$phar->buildFromDirectory(__DIR__);

// Create a custom stub to add the shebang
$stub = "#!$shebang \n".$defaultStub;

// Add the stub
$phar->setStub($stub);

$phar->stopBuffering();