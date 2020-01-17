<?php
/**
 * This file contains the LegacyDrafterTest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Parse\LegacyDrafter;
use ReflectionClass;

/**
 * Class LegacyDrafterTest
 * @covers \PHPDraft\Parse\LegacyDrafter
 */
class LegacyDrafterTest extends LunrBaseTest
{

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->mock_function('sys_get_temp_dir', function() { return TEST_STATICS; });
        $this->mock_function('shell_exec', function() { return "/some/dir/drafter\n";});
        $this->class      = new LegacyDrafter();
        $this->reflection = new ReflectionClass('PHPDraft\Parse\LegacyDrafter');
        $this->class->init(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'));
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
    }

    /**
     * Test if the value the class is initialized with is correct
     */
    public function testSetupCorrectly(): void
    {
        $property = $this->reflection->getProperty('apib');
        $property->setAccessible(TRUE);
        $this->assertEquals(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'), $property->getValue($this->class));
    }

    /**
     * Check if the JSON is empty before parsing
     */
    public function testPreRunStringIsEmpty(): void
    {
        $this->assertEmpty($this->class->json);
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     */
    public function testParseToJSON(): void
    {
        $this->mock_function('json_last_error', function() { return JSON_ERROR_NONE;});
        $this->mock_function('shell_exec', function() { return "";});
        file_put_contents(TEST_STATICS . '/drafter/index.json', file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $this->class->parseToJson();
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')), $this->class->json);
        $this->unmock_function('shell_exec');
        $this->unmock_function('json_last_error');
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result with inheritance
     */
    public function testParseToJSONInheritance(): void
    {
        $this->mock_function('json_last_error', function() { return JSON_ERROR_NONE;});
        $this->mock_function('shell_exec', function() { return "";});
        file_put_contents(TEST_STATICS . '/drafter/index.json', file_get_contents(TEST_STATICS . '/drafter/json/inheritance.json'));
        $this->class->parseToJson();
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/inheritance.json')), $this->class->json);
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

        $this->mock_function('shell_exec', function() { return "";});
        file_put_contents(TEST_STATICS . '/drafter/index.json',
            file_get_contents(TEST_STATICS . '/drafter/json/error.json'));
        $this->class->parseToJson();
        $this->expectOutputString("WARNING: ignoring unrecognized block\nWARNING: no headers specified\nWARNING: ignoring unrecognized block\nWARNING: empty request message-body");
        $this->unmock_function('shell_exec');
    }

    /**
     * Check if parsing the fails without drafter
     *
     * @covers \PHPDraft\Parse\Drafter::available()
     */
    public function testAvailableIsFalseWhenNoDrafter(): void
    {
        $this->mock_function('shell_exec', function() { return "";});
        $this->assertFalse(LegacyDrafter::available());
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

        $this->mock_function('json_last_error', function() { return JSON_ERROR_DEPTH;});
        $this->mock_function('json_last_error_msg', function() { return "ERROR";});
        file_put_contents(TEST_STATICS . '/drafter/index.json', '["hello: \'world}');
        $this->class->parseToJson();
        $this->expectOutputString('ERROR: invalid json in /tmp/drafter/index.json');
        $this->unmock_function('json_last_error_msg');
        $this->unmock_function('json_last_error');
    }

}
