<?php

/**
 * This file contains the DrafterTest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\In\ApibFileParser;
use PHPDraft\Parse\Drafter;
use PHPDraft\Parse\ExecutionException;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;

/**
 * Class DrafterTest
 *
 * @covers \PHPDraft\Parse\Drafter
 */
class DrafterTest extends LunrBaseTest
{
    /**
     * Shared instance of the file parser.
     *
     * @var ApibFileParser|MockObject
     */
    private mixed $parser;

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->mock_function('sys_get_temp_dir', fn() => TEST_STATICS);
        $this->mock_function('shell_exec', fn() => "/some/dir/drafter\n");

        $this->parser = $this->getMockBuilder('\PHPDraft\In\ApibFileParser')
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->parser->set_apib_content(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'));

        $this->class      = new Drafter();
        $this->baseSetUp($this->class);

        $this->class->init($this->parser);

        $this->unmock_function('shell_exec');
        $this->unmock_function('sys_get_temp_dir');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        if (file_exists(TEST_STATICS . '/drafter/index.json')) {
            unlink(TEST_STATICS . '/drafter/index.json');
        }
        if (file_exists(TEST_STATICS . '/drafter/index.apib')) {
            unlink(TEST_STATICS . '/drafter/index.apib');
        }
        unset($this->class);
        unset($this->reflection);
        unset($this->parser);
    }

    /**
     * Test if the value the class is initialized with is correct
     *
     * @covers \PHPDraft\Parse\Drafter::parseToJson()
     */
    public function testSetupCorrectly(): void
    {
        $this->assertInstanceOf('\PHPDraft\In\ApibFileParser', $this->get_reflection_property_value('apib'));
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     */
    public function testParseToJSON(): void
    {
        $this->mock_function('json_last_error', fn() => JSON_ERROR_NONE);
        $this->mock_function('shell_exec', fn() => "");
        file_put_contents(
            TEST_STATICS . '/drafter/index.json',
            file_get_contents(TEST_STATICS . '/drafter/json/index.json')
        );
        $this->class->parseToJson();
        $this->assertEquals(
            json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')),
            $this->class->json
        );
        $this->unmock_function('shell_exec');
        $this->unmock_function('json_last_error');
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result with inheritance
     *
     * @covers \PHPDraft\Parse\Drafter::parseToJson()
     */
    public function testParseToJSONInheritance(): void
    {
        $this->mock_function('json_last_error', fn() => JSON_ERROR_NONE);
        $this->mock_function('shell_exec', fn() => '');
        file_put_contents(
            TEST_STATICS . '/drafter/index.json',
            file_get_contents(TEST_STATICS . '/drafter/json/inheritance.json')
        );
        $this->class->parseToJson();
        $this->assertEquals(
            json_decode(file_get_contents(TEST_STATICS . '/drafter/json/inheritance.json')),
            $this->class->json
        );
        $this->unmock_function('shell_exec');
        $this->unmock_function('json_last_error');
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     *
     * @covers \PHPDraft\Parse\Drafter::parseToJson()
     */
    public function testParseToJSONWithErrors(): void
    {
        $this->expectException('\PHPDraft\Parse\ExecutionException');
        $this->expectExceptionMessage('Parsing encountered errors and stopped');
        $this->expectExceptionCode(2);

        $this->mock_function('shell_exec', fn() => '');
        file_put_contents(
            TEST_STATICS . '/drafter/index.json',
            file_get_contents(TEST_STATICS . '/drafter/json/error.json')
        );
        $this->class->parseToJson();
        $this->expectOutputString("WARNING: ignoring unrecognized block\nWARNING: no headers specified\nWARNING: ignoring unrecognized block\nWARNING: empty request message-body");
        $this->unmock_function('shell_exec');
    }

    /**
     * Check if parsing the fails without drafter
     *
     * @covers \PHPDraft\Parse\Drafter::available()
     */
    public function testSetupWithoutDrafter(): void
    {
        $this->mock_function('shell_exec', fn() => '');
        $this->assertFalse(Drafter::available());
        $this->unmock_function('shell_exec');
    }

    /**
     * Check if parsing the fails when invalid JSON
     *
     * @covers \PHPDraft\Parse\Drafter::parseToJson()
     */
    public function testParseToJSONWithInvalidJSON(): void
    {
        $this->expectException('\PHPDraft\Parse\ExecutionException');
        $this->expectExceptionMessage('Drafter generated invalid JSON (ERROR)');
        $this->expectExceptionCode(2);

        $this->mock_function('json_last_error', fn() => JSON_ERROR_DEPTH);
        $this->mock_function('json_last_error_msg', fn() => 'ERROR');
        file_put_contents(TEST_STATICS . '/drafter/index.json', '["hello: \'world}');
        $this->class->parseToJson();
        $this->expectOutputString('ERROR: invalid json in /tmp/drafter/index.json');
        $this->unmock_function('json_last_error_msg');
        $this->unmock_function('json_last_error');
    }
}
