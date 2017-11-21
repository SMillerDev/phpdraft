<?php
/**
 * This file contains the DrafterTest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\Parse\Drafter;
use ReflectionClass;

/**
 * Class DrafterTest
 * @covers \PHPDraft\Parse\Drafter
 */
class DrafterTest extends BaseTest
{

    /**
     * Set up
     */
    public function setUp()
    {
        $this->mock_function('sys_get_temp_dir', TEST_STATICS);
        $this->mock_function('shell_exec', "/some/dir/drafter\n");
        $this->class      = new Drafter(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'));
        $this->reflection = new ReflectionClass('PHPDraft\Parse\Drafter');
        $this->unmock_function('shell_exec');
        $this->unmock_function('sys_get_temp_dir');
    }

    /**
     * Tear down
     */
    public function tearDown()
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
    public function testSetupCorrectly()
    {
        $property = $this->reflection->getProperty('apib');
        $property->setAccessible(TRUE);
        $this->assertEquals(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'), $property->getValue($this->class));
    }

    /**
     * Check if the JSON is empty before parsing
     */
    public function testPreRunStringIsEmpty()
    {
        $this->assertEmpty($this->class->json);
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     */
    public function testParseToJSON()
    {
        $this->mock_function('json_last_error', JSON_ERROR_NONE);
        $this->mock_function('shell_exec', "");
        file_put_contents(TEST_STATICS . '/drafter/index.json', file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $this->class->parseToJson();
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')), $this->class->json);
        $this->unmock_function('shell_exec');
        $this->unmock_function('json_last_error');
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result with inheritance
     */
    public function testParseToJSONInheritance()
    {
        $this->mock_function('json_last_error', JSON_ERROR_NONE);
        $this->mock_function('shell_exec', "");
        file_put_contents(TEST_STATICS . '/drafter/index.json', file_get_contents(TEST_STATICS . '/drafter/json/inheritance.json'));
        $this->class->parseToJson();
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/inheritance.json')), $this->class->json);
        $this->unmock_function('shell_exec');
        $this->unmock_function('json_last_error');
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     *
     * @covers                      \PHPDraft\Parse\Drafter::parseToJson()
     * @expectedException           \RuntimeException
     * @expectedExceptionMessage    Parsing encountered errors and stopped
     * @expectedExceptionCode       2
     */
    public function testParseToJSONWithErrors()
    {
        $this->mock_function('shell_exec', "");
        file_put_contents(TEST_STATICS . '/drafter/index.json',
            file_get_contents(TEST_STATICS . '/drafter/json/error.json'));
        $this->class->parseToJson();
        $this->expectOutputString("WARNING: ignoring unrecognized block\nWARNING: no headers specified\nWARNING: ignoring unrecognized block\nWARNING: empty request message-body");
        $this->unmock_function('shell_exec');
    }

    /**
     * Check if parsing the fails without drafter
     *
     * @covers                   \PHPDraft\Parse\Drafter::parseToJson()
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Drafter was not installed!
     * @expectedExceptionCode    1
     */
    public function testSetupWithoutDrafter()
    {
        $this->mock_function('shell_exec', "");
        new Drafter('hello');
        $this->unmock_function('shell_exec');
    }

    /**
     * Check if parsing the fails when invalid JSON
     *
     * @covers                   \PHPDraft\Parse\Drafter::parseToJson()
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Drafter generated invalid JSON (ERROR)
     * @expectedExceptionCode    2
     */
    public function testParseToJSONWithInvalidJSON()
    {
        $this->mock_function('json_last_error', JSON_ERROR_DEPTH);
        $this->mock_function('json_last_error_msg', "ERROR");
        file_put_contents(TEST_STATICS . '/drafter/index.json', '["hello: \'world}');
        $this->class->parseToJson();
        $this->expectOutputString('ERROR: invalid json in /tmp/drafter/index.json');
        $this->unmock_function('json_last_error_msg');
        $this->unmock_function('json_last_error');
    }

}
