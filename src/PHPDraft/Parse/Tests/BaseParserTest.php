<?php
/**
 * This file contains the BaseParserTest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use Lunr\Halo\LunrBaseTest;
use ReflectionClass;

/**
 * Class BaseParserTest
 * @covers \PHPDraft\Parse\BaseParser
 */
class BaseParserTest extends LunrBaseTest
{

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->mock_function('sys_get_temp_dir', function () {return TEST_STATICS;});
        $this->mock_function('shell_exec', function () {return "/some/dir/drafter\n";});
        $this->class      = $this->getMockBuilder('\PHPDraft\Parse\BaseParser')
                                 ->getMockForAbstractClass();
        $this->class->init(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'));
        $this->reflection = new ReflectionClass($this->class);
        $this->unmock_function('shell_exec');
        $this->unmock_function('sys_get_temp_dir');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Test if the value the class is initialized with is correct
     *
     * @covers \PHPDraft\Parse\Drafter::parseToJson()
     */
    public function testSetupCorrectly(): void
    {
        $property = $this->reflection->getProperty('apib');
        $property->setAccessible(TRUE);
        $this->assertEquals(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'), $property->getValue($this->class));
    }

    /**
     * Check if the JSON is empty before parsing
     *
     * @covers \PHPDraft\Parse\Drafter::parseToJson()
     */
    public function testPreRunStringIsEmpty(): void
    {
        $this->assertEmpty($this->class->json);
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     *
     * @covers \PHPDraft\Parse\Drafter::parseToJson()
     */
    public function testParseToJSON(): void
    {
        $this->class->expects($this->once())
                    ->method('parse')
                    ->will($this->returnValue(NULL));
        $this->class->json = json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $this->class->parseToJson();
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')), $this->class->json);
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     *
     * @covers \PHPDraft\Parse\Drafter::parseToJson()
     */
    public function testParseToJSONMkDir(): void
    {
        $this->class->expects($this->once())
                    ->method('parse')
                    ->will($this->returnValue(NULL));
        $this->class->json = json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $this->class->parseToJson();
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')), $this->class->json);
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     *
     * @covers \PHPDraft\Parse\Drafter::parseToJson()
     */
    public function testParseToJSONMkTmp(): void
    {
        $tmp_dir = dirname(dirname(TEST_STATICS)) . '/build/tmp';
        if (file_exists($tmp_dir . DIRECTORY_SEPARATOR . 'index.apib')){
            unlink($tmp_dir . DIRECTORY_SEPARATOR . 'index.apib');
        }
        if (file_exists($tmp_dir)){
            rmdir($tmp_dir);
        }
        $property = $this->reflection->getProperty('tmp_dir');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $tmp_dir);
        $this->class->expects($this->once())
                    ->method('parse')
                    ->will($this->returnValue(NULL));
        $this->class->json = json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $this->class->parseToJson();
        $this->assertDirectoryExists($tmp_dir);
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json')), $this->class->json);
    }

    /**
     * Check if parsing the fails when invalid JSON
     *
     * @covers \PHPDraft\Parse\Drafter::parseToJson()
     */
    public function testParseToJSONWithInvalidJSON(): void
    {
        $this->class->expects($this->once())
                    ->method('parse')
                    ->will($this->returnValue(NULL));

        $this->expectException('\PHPDraft\Parse\ExecutionException');
        $this->expectExceptionMessage('Drafter generated invalid JSON (ERROR)');
        $this->expectExceptionCode(2);

        $this->mock_function('json_last_error', function () {return JSON_ERROR_DEPTH;});
        $this->mock_function('json_last_error_msg', function () {return "ERROR";});
        $this->class->parseToJson();
        $this->expectOutputString('ERROR: invalid json in /tmp/drafter/index.json');
        $this->unmock_function('json_last_error_msg');
        $this->unmock_function('json_last_error');
    }
}
