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

    /**
     * Set up tests.
     *
     * @return void Test is now set up.
     */
    public function setUp(): void
    {
        $this->class      = new ApibFileParser(__DIR__ . '/ApibFileParserTest.php');
        $this->reflection = new ReflectionClass('PHPDraft\In\ApibFileParser');
    }

    /**
     * Test if setup is successful
     * @return void
     */
    public function testLocationSetup(): void
    {
        $property = $this->reflection->getProperty('location');
        $property->setAccessible(true);
        $this->assertSame(__DIR__ . '/', $property->getValue($this->class));
    }

    /**
     * Test if setup is successful
     * @return void
     */
    public function testFilenameSetup(): void
    {
        $property = $this->reflection->getProperty('filename');
        $property->setAccessible(true);
        $this->assertSame(__DIR__ . '/ApibFileParserTest.php', $property->getValue($this->class));
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

        $property = $this->reflection->getProperty('filename');
        $property->setAccessible(true);
        $property->setValue($this->class, TEST_STATICS . '/drafter/non_existing_including_apib');
        $this->class->parse();
    }

    /**
     * Test if setup is successful
     * @return void
     */
    public function testParseBasic(): void
    {
        $property = $this->reflection->getProperty('filename');
        $property->setAccessible(true);
        $property->setValue($this->class, TEST_STATICS . '/drafter/apib/including.apib');
        $loc_property = $this->reflection->getProperty('location');
        $loc_property->setAccessible(true);
        $loc_property->setValue($this->class, TEST_STATICS . '/drafter/apib/');

        $this->mock_function('curl_exec', function () {
            return 'hello';
        });
        $this->class->parse();
        $this->unmock_function('curl_exec');

        $full_property = $this->reflection->getProperty('full_apib');
        $full_property->setAccessible(true);

        $text = "FORMAT: 1A\nHOST: https://owner-api.teslamotors.com\n";
        $text .= "EXTRA_HOSTS: https://test.owner-api.teslamotors.com\nSOMETHING: INFO\n\n";
        $text .= "# Tesla Model S JSON API\nThis is unofficial documentation of the";
        $text .= " Tesla Model S JSON API used by the iOS and Android apps. It features";
        $text .= " functionality to monitor and control the Model S remotely.\n\nTEST";
        $text .= "\n\n# Hello\nThis is a test.\nhello";

        $this->assertSame($text, $full_property->getValue($this->class));
        $this->assertSame($text, $this->class->__toString());
    }

    /**
     * Test setting content
     *
     * @covers \PHPDraft\In\ApibFileParser::set_apib_content
     */
    public function testSetContent(){
        $this->class->set_apib_content('content');
        $this->assertEquals('content', $this->get_reflection_property_value('full_apib'));
    }
}
