<?php
/**
 * This file contains the DrafterAPITest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use PHPDraft\Core\BaseTest;
use PHPDraft\Parse\Drafter;
use PHPDraft\Parse\DrafterAPI;
use ReflectionClass;
use SebastianBergmann\GlobalState\RuntimeException;

/**
 * Class DrafterAPITest
 * @covers \PHPDraft\Parse\DrafterAPI
 */
class DrafterAPITest extends BaseTest
{

    /**
     * Set up
     *
     * @param int $code curl code
     */
    public function setUpWith($code)
    {
        $this->mock_function('curl_errno', $code);
        $this->setUpBasic();
        $this->unmock_function('curl_errno');
    }

    public function setUpBasic()
    {
        $this->mock_function('sys_get_temp_dir', TEST_STATICS);
        $this->mock_function('curl_exec', "/some/dir/drafter\n");
        $this->class      = new DrafterAPI(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'));
        $this->reflection = new ReflectionClass('PHPDraft\Parse\DrafterAPI');
        $this->unmock_function('curl_exec');
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
        $this->setUpWith(0);
        $property = $this->reflection->getProperty('apib');
        $property->setAccessible(TRUE);
        $this->assertEquals(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'), $property->getValue($this->class));
    }

    /**
     * Test if the value the class is initialized with is correct
     *
     * @expectedException RuntimeException
     * @expectedExceptionMessage Drafter webservice is not available!
     * @expectedExceptionCode    1
     */
    public function testSetupFailed()
    {
        $this->setUpWith(1);

        $property = $this->reflection->getProperty('apib');
        $property->setAccessible(TRUE);
        $this->assertEquals(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'), $property->getValue($this->class));
    }

    /**
     * Check if the JSON is empty before parsing
     */
    public function testPreRunStringIsEmpty()
    {
        $this->setUpWith(0);
        $this->assertEmpty($this->class->json);
    }

    /**
     * Check if parsing the fails without drafter
     *
     * @covers                   \PHPDraft\Parse\DrafterAPI::parseToJson()
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Drafter webservice failed to parse input
     * @expectedExceptionCode    1
     */
    public function testParseWithFailingWebservice()
    {
        $this->setUpWith(0);
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
        $this->setUpWith(0);
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
