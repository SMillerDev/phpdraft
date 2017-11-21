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
 * @covers \PHPDraft\Out\UI
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
    public function testReleaseIDIsNull()
    {
        define('VERSION', '0');
        $this->mock_function('exec', 12);
        $return = $this->class->release_id();
        $this->assertSame(12, $return);
        $this->unmock_function('exec');
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testReleaseIDIsNotNull()
    {
        $this->redefine('VERSION', '1.2.3');
        $return = $this->class->release_id();
        $this->assertSame('1.2.3', $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testVersion()
    {
        $this->redefine('VERSION', '1.2.4');
        $this->class->version();
        $this->expectOutputString('PHPDraft: 1.2.4');
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testHelp()
    {
        $this->class->help();
        $this->expectOutputString(file_get_contents(TEST_STATICS.'/drafter/help.txt'));
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSeries()
    {
        $this->redefine('VERSION', '1.2.4');
        $return = $this->class->series();
        $this->assertSame('1.2', $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testReleaseChannel()
    {
        $this->redefine('VERSION', '1.2.4-beta');
        $return = $this->class->getReleaseChannel();
        $this->assertSame('-nightly', $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testReleaseChannelNormal()
    {
        $this->redefine('VERSION', '1.2.4');
        $return = $this->class->getReleaseChannel();
        $this->assertSame('', $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSeriesNightly()
    {
        $this->redefine('VERSION', '1.2.4-beta');
        $return = $this->class->series();
        $this->assertSame('1.2', $return);
    }
}