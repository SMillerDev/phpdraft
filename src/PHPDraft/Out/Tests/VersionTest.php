<?php

/**
 * This file contains the VersionTest.php
 *
 * @package PHPDraft\Out
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Out\Version;
use ReflectionClass;

/**
 * Class VersionTest
 *
 * @covers \PHPDraft\Out\Version
 */
class VersionTest extends LunrBaseTest
{
    /**
     * Set up tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class      = new Version();
        $this->reflection = new ReflectionClass('PHPDraft\Out\Version');
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testReleaseIDIsNull(): void
    {
        $this->constant_redefine('VERSION', '0');
        $this->mock_function('exec', fn() => '12');
        $return = $this->class->release_id();
        $this->assertSame('12', $return);
        $this->unmock_function('exec');
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testReleaseIDIsNotNull(): void
    {
        $this->constant_redefine('VERSION', '1.2.3');
        $return = $this->class->release_id();
        $this->assertSame('1.2.3', $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testVersion(): void
    {
        $this->constant_redefine('VERSION', '1.2.4');
        $this->class->version();
        $this->expectOutputString('PHPDraft: 1.2.4');
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSeries(): void
    {
        $this->constant_redefine('VERSION', '1.2.4');
        $return = $this->class->series();
        $this->assertSame('1.2', $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testReleaseChannel(): void
    {
        $this->constant_redefine('VERSION', '1.2.4-beta');
        $return = $this->class->getReleaseChannel();
        $this->assertSame('-nightly', $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testReleaseChannelNormal(): void
    {
        $this->constant_redefine('VERSION', '1.2.4');
        $return = $this->class->getReleaseChannel();
        $this->assertSame('', $return);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSeriesNightly(): void
    {
        $this->constant_redefine('VERSION', '1.2.4-beta');
        $return = $this->class->series();
        $this->assertSame('1.2', $return);
    }
}
