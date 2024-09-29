<?php

/**
 * This file contains the ApibFileParserTest.php
 *
 * @package PHPDraft\In
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\In\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\In\ApibFileParser;
use ReflectionClass;

/**
 * Class ApibFileParserTest
 * @covers \PHPDraft\In\ApibFileParser
 */
class ApibFileParserTest extends LunrBaseTest
{

    private ApibFileParser $class;

    /**
     * Set up tests.
     *
     * @return void Test is now set up.
     */
    public function setUp(): void
    {
        $this->class = new ApibFileParser(__DIR__ . '/ApibFileParserTest.php');
        $this->baseSetUp($this->class);
    }

    /**
     * Test if setup is successful
     * @return void
     */
    public function testLocationSetup(): void
    {
        $this->assertPropertyEquals('location', __DIR__ . '/');
    }

    /**
     * Test if setup is successful
     * @return void
     */
    public function testFilenameSetup(): void
    {
        $this->assertPropertySame('filename', __DIR__ . '/ApibFileParserTest.php');
    }

    /**
     * Test if exception when the file doesn't exist
     *
     * @return void
     */
    public function testFilenameSetupWrong(): void
    {
        $this->expectException('\PHPDraft\Parse\ExecutionException');
        $this->expectExceptionMessageMatches('/API File not found: .*\/drafter\/non_existing_including_apib/');
        $this->expectExceptionCode(1);

        $this->set_reflection_property_value('filename', TEST_STATICS . '/drafter/non_existing_including_apib');
        $this->class->parse();
    }

    /**
     * Test if setup is successful
     * @return void
     */
    public function testParseBasic(): void
    {
        $this->set_reflection_property_value('filename', TEST_STATICS . '/drafter/apib/including.apib');
        $this->set_reflection_property_value('location', TEST_STATICS . '/drafter/apib/');


        $this->mock_function('curl_exec', fn() => 'hello');

        $this->class->parse();

        $this->unmock_function('curl_exec');

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
     *
     * @covers \PHPDraft\In\ApibFileParser::set_apib_content
     */
    public function testSetContent(): void
    {
        $this->class->set_apib_content('content');
        $this->assertEquals('content', $this->get_reflection_property_value('full_apib'));
    }
}
