<?php

/**
 * This file contains the BaseParserTest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\In\ApibFileParser;
use PHPDraft\Parse\BaseParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class BaseParserTest
 */
#[CoversClass(BaseParser::class)]
class BaseParserTest extends LunrBaseTestCase
{
    /**
     * Shared instance of the file parser.
     *
     * @var ApibFileParser&MockObject
     */
    private ApibFileParser&MockObject $parser;

    /**
     * Shared instance of the base parser.
     *
     * @var BaseParser&MockObject
     */
    private BaseParser $class;

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->mockFunction('sys_get_temp_dir', fn() => TEST_STATICS);
        $this->mockFunction('shell_exec', fn() => "/some/dir/drafter\n");

        $this->parser = $this->getMockBuilder('\PHPDraft\In\ApibFileParser')
                             ->disableOriginalConstructor()
                             ->getMock();
        $this->class  = new class extends BaseParser {
            protected function parse(): void
            {
            }

            public static function available(): bool
            {
                return true;
            }
        };

        $this->parser->set_apib_content(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'));

        $this->class->init($this->parser);
        $this->baseSetUp($this->class);

        $this->unmockFunction('shell_exec');
        $this->unmockFunction('sys_get_temp_dir');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->class);
        unset($this->reflection);
        unset($this->parser);
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly(): void
    {
        $this->assertInstanceOf('\PHPDraft\In\ApibFileParser', $this->getReflectionPropertyValue('apib'));
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     */
    public function testParseToJSON(): void
    {
        $this->class->json = json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $this->class->parseToJson();
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')), $this->class->json);
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     */
    public function testParseToJSONMkDir(): void
    {
        $this->class->json = json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $this->class->parseToJson();
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')), $this->class->json);
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     */
    public function testParseToJSONMkTmp(): void
    {
        $tmp_dir = dirname(TEST_STATICS, 2) . '/build/tmp';
        if (file_exists($tmp_dir . DIRECTORY_SEPARATOR . 'index.apib')) {
            unlink($tmp_dir . DIRECTORY_SEPARATOR . 'index.apib');
        }
        if (file_exists($tmp_dir)) {
            rmdir($tmp_dir);
        }

        $this->setReflectionPropertyValue('tmp_dir', $tmp_dir);

        $this->class->json = json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $this->class->parseToJson();
        $this->assertDirectoryExists($tmp_dir);
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')), $this->class->json);
    }

    /**
     * Check if parsing the fails when invalid JSON
     */
    public function testParseToJSONWithInvalidJSON(): void
    {
        $this->expectException('\PHPDraft\Parse\ExecutionException');
        $this->expectExceptionMessage('Drafter generated invalid JSON (ERROR)');
        $this->expectExceptionCode(2);

        $this->mockFunction('json_last_error', fn() => JSON_ERROR_DEPTH);
        $this->mockFunction('json_last_error_msg', fn() => "ERROR");
        $this->class->parseToJson();
        $this->expectOutputString('ERROR: invalid json in /tmp/drafter/index.json');
        $this->unmockFunction('json_last_error_msg');
        $this->unmockFunction('json_last_error');
    }
}
