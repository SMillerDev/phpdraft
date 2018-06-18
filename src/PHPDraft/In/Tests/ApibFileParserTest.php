<?php
/**
 * This file contains the ApibFileParserTest.php
 *
 * @package PHPDraft\In
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\In\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\In\ApibFileParser;
use ReflectionClass;

/**
 * Class ApibFileParserTest
 * @covers \PHPDraft\In\ApibFileParser
 */
class ApibFileParserTest extends BaseTest
{

    /**
     * Set up tests.
     *
     * @return void Test is now set up.
     */
    public function setUp()
    {
        $this->class      = new ApibFileParser(__DIR__ . '/ApibFileParserTest.php');
        $this->reflection = new ReflectionClass('PHPDraft\In\ApibFileParser');
    }

    /**
     * Test if setup is successful
     * @return void
     */
    public function testLocationSetup()
    {
        $property = $this->reflection->getProperty('location');
        $property->setAccessible(true);
        $this->assertSame(__DIR__ . '/', $property->getValue($this->class));
    }

    /**
     * Test if setup is successful
     * @return void
     */
    public function testFilenameSetup()
    {
        $property = $this->reflection->getProperty('filename');
        $property->setAccessible(true);
        $this->assertSame(__DIR__ . '/ApibFileParserTest.php', $property->getValue($this->class));
    }

    /**
     * Test if exception when the file doesn't exist
     * @expectedException \PHPDraft\Parse\ExecutionException
     * @expectedExceptionCode 1
     * @expectedExceptionMessageRegExp "API File not found: [\w\W]*\/drafter\/non_existing_including_apib"
     *
     * @return void
     */
    public function testFilenameSetupWrong()
    {
        $property = $this->reflection->getProperty('filename');
        $property->setAccessible(true);
        $property->setValue($this->class, TEST_STATICS . '/drafter/non_existing_including_apib');
        $this->class->parse();
    }

    /**
     * Test if setup is successful
     * @return void
     */
    public function testParseBasic()
    {
        $property = $this->reflection->getProperty('filename');
        $property->setAccessible(true);
        $property->setValue($this->class, TEST_STATICS . '/drafter/apib/including.apib');
        $loc_property = $this->reflection->getProperty('location');
        $loc_property->setAccessible(true);
        $loc_property->setValue($this->class, TEST_STATICS . '/drafter/apib/');

        $this->mock_function('curl_exec', 'hello');
        $this->class->parse();
        $this->unmock_function('curl_exec');

        $full_property = $this->reflection->getProperty('full_apib');
        $full_property->setAccessible(true);

        $text = "FORMAT: 1A\nHOST: https://owner-api.teslamotors.com\nEXTRA_HOSTS: https://test.owner-api.teslamotors.com\nSOMETHING: INFO\n\n";
        $text .="# Tesla Model S JSON API\nThis is unofficial documentation of the Tesla Model S JSON API used by the iOS and Android apps. It features functionality to monitor and control the Model S remotely.\n\nTEST";
        $text .="\n\n# Hello\nThis is a test.\nhello";

        $this->assertSame($text, $full_property->getValue($this->class));
        $this->assertSame($text, $this->class->__toString());
    }

}