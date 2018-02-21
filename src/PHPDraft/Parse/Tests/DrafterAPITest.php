<?php
/**
 * This file contains the DrafterAPITest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\Parse\DrafterAPI;

use ReflectionClass;

/**
 * Class DrafterAPITest
 * @covers \PHPDraft\Parse\DrafterAPI
 */
class DrafterAPITest extends BaseTest
{
    /**
     * Basic setup
     */
    public function setUp()
    {
        $this->mock_function('sys_get_temp_dir', TEST_STATICS);
        $this->class      = new DrafterAPI();
        $this->reflection = new ReflectionClass('PHPDraft\Parse\DrafterAPI');
        $this->class->init(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'));
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
        $this->assertEquals(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'), $property->getValue($this->class));
    }

    /**
     * Test if the drafter api can be used
     *
     */
    public function testAvailableFails()
    {
        $this->mock_function('curl_exec', "/some/dir/drafter\n");
        $this->mock_function('curl_errno', 1);

        $this->assertFalse(DrafterAPI::available());

        $this->unmock_function('curl_errno');
        $this->unmock_function('curl_exec');
    }

    /**
     * Test if the drafter api can be used
     *
     */
    public function testAvailableSuccess()
    {
        $this->mock_function('curl_exec', "/some/dir/drafter\n");
        $this->mock_function('curl_errno', 0);

        $this->assertFalse(DrafterAPI::available());

        $this->unmock_function('curl_errno');
        $this->unmock_function('curl_exec');
    }

    /**
     * Check if the JSON is empty before parsing
     */
    public function testPreRunStringIsEmpty()
    {
        $this->assertEmpty($this->class->json);
    }

    /**
     * Check if parsing the fails without drafter
     *
     * @covers                   \PHPDraft\Parse\DrafterAPI::parseToJson()
     * @expectedException        \PHPDraft\Parse\ResourceException
     * @expectedExceptionMessage Drafter webservice failed to parse input
     * @expectedExceptionCode    1
     */
    public function testParseWithFailingWebservice()
    {
        $this->mock_function('curl_errno', 1);
        $this->class->parseToJson();
        $this->unmock_function('curl_errno');
    }

    /**
     * Check if parsing the succeeds
     *
     * @covers \PHPDraft\Parse\DrafterAPI::parseToJson()
     */
    public function testParseSuccess()
    {
        $this->mock_function('json_last_error', 0);
        $this->mock_function('curl_errno', 0);
        $this->mock_function('curl_exec', '{"content":[{"element":"world"}]}');
        $this->class->parseToJson();
        $this->unmock_function('curl_exec');
        $this->unmock_function('curl_errno');
        $this->unmock_function('json_last_error');
        $obj           = new \stdClass();
        $obj2          = new \stdClass();
        $obj2->element = 'world';
        $obj->content  = [ $obj2 ];
        $this->assertEquals($obj, $this->class->json);
    }

}
