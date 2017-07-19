<?php
/**
 * This file contains the BaseParserTest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\Parse\Drafter;
use ReflectionClass;

/**
 * Class BaseParserTest
 * @covers \PHPDraft\Parse\BaseParser
 */
class BaseParserTest extends BaseTest
{

    /**
     * Set up
     */
    public function setUp()
    {
        $this->mock_function('sys_get_temp_dir', TEST_STATICS);
        $this->mock_function('shell_exec', "/some/dir/drafter\n");
        $this->class      = $this->getMockBuilder('\PHPDraft\Parse\BaseParser')
                                 ->setConstructorArgs([file_get_contents(TEST_STATICS . '/drafter/apib')])
                                 ->getMockForAbstractClass();
        $this->reflection = new ReflectionClass($this->class);
        $this->unmock_function('shell_exec');
        $this->unmock_function('sys_get_temp_dir');
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
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
        $this->assertEquals(file_get_contents(TEST_STATICS . '/drafter/apib'), $property->getValue($this->class));
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
        $this->class->expects($this->once())
                    ->method('parse')
                    ->will($this->returnValue(NULL));
        $this->class->json = json_decode(file_get_contents(TEST_STATICS . '/drafter/json'));
        $this->class->parseToJson();
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json')), $this->class->json);
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     */
    public function testParseToJSONMkDir()
    {
        $this->class->expects($this->once())
                    ->method('parse')
                    ->will($this->returnValue(NULL));
        $this->class->json = json_decode(file_get_contents(TEST_STATICS . '/drafter/json'));
        $this->class->parseToJson();
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json')), $this->class->json);
    }

    /**
     * Check if parsing the APIB to JSON gives the expected result
     */
    public function testParseToJSONMkTmp()
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
        $this->class->json = json_decode(file_get_contents(TEST_STATICS . '/drafter/json'));
        $this->class->parseToJson();
        $this->assertDirectoryExists($tmp_dir);
        $this->assertEquals(json_decode(file_get_contents(TEST_STATICS . '/drafter/json')), $this->class->json);
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
        $this->class->expects($this->once())
                    ->method('parse')
                    ->will($this->returnValue(NULL));
        $this->mock_function('json_last_error', JSON_ERROR_DEPTH);
        $this->mock_function('json_last_error_msg', "ERROR");
        $this->class->parseToJson();
        $this->expectOutputString('ERROR: invalid json in /tmp/drafter/index.json');
        $this->unmock_function('json_last_error_msg');
        $this->unmock_function('json_last_error');
    }
}
