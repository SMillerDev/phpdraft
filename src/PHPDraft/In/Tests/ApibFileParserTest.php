<?php

/**
 * This file contains the ApibFileParserTest.php
 *
 * @package PHPDraft\In
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\In\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\In\ApibFileParser;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class ApibFileParserTest
 */
#[CoversClass(ApibFileParser::class)]
class ApibFileParserTest extends LunrBaseTestCase
{
    private ApibFileParser $class;

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->class = new ApibFileParser(__DIR__ . '/ApibFileParserTest.php');
        $this->baseSetUp($this->class);
    }

    /**
     * Test if setup is successful
     */
    public function testLocationSetup(): void
    {
        $this->assertPropertyEquals('location', __DIR__ . '/');
    }

    /**
     * Test if setup is successful
     */
    public function testFilenameSetup(): void
    {
        $this->assertPropertySame('filename', __DIR__ . '/ApibFileParserTest.php');
    }

    /**
     * Test if exception when the file doesn't exist
     */
    public function testFilenameSetupWrong(): void
    {
        $this->expectException('\PHPDraft\Parse\ExecutionException');
        $this->expectExceptionMessageMatches('/API File not found: .*\/drafter\/non_existing_including_apib/');
        $this->expectExceptionCode(1);

        $this->setReflectionPropertyValue('filename', TEST_STATICS . '/drafter/non_existing_including_apib');
        $this->class->parse();
    }

    /**
     * Test if setup is successful
     */
    public function testParseBasic(): void
    {
        $this->setReflectionPropertyValue('filename', TEST_STATICS . '/drafter/apib/including.apib');
        $this->setReflectionPropertyValue('location', TEST_STATICS . '/drafter/apib/');


        $this->mockFunction('curl_exec', fn() => 'hello');

        $this->class->parse();

        $this->unmockFunction('curl_exec');

        $text = "FORMAT: 1A\nHOST: https://owner-api.teslamotors.com\n";
        $text .= "EXTRA_HOSTS: https://test.owner-api.teslamotors.com\nSOMETHING: INFO\n\n";
        $text .= "# Tesla Model S JSON API\nThis is unofficial documentation of the";
        $text .= " Tesla Model S JSON API used by the iOS and Android apps. It features";
        $text .= " functionality to monitor and control the Model S remotely.\n\nTEST";
        $text .= "\n\n# Hello\nThis is a test.\nhello";

        $this->assertPropertyEquals('full_apib', $text);
        $this->assertSame($text, $this->class->__toString());
    }

    /**
     * Test setting content
     */
    public function testSetContent(): void
    {
        $this->class->set_apib_content('content');
        $this->assertEquals('content', $this->getReflectionPropertyValue('full_apib'));
    }
}
