<?php

/**
 * This file contains the DrafterAPITest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\In\ApibFileParser;
use PHPDraft\Parse\DrafterAPI;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;

/**
 * Class DrafterAPITest
 * @covers \PHPDraft\Parse\DrafterAPI
 */
class DrafterAPITest extends LunrBaseTest
{

    /**
     * Shared instance of the file parser.
     *
     * @var ApibFileParser|MockObject
     */
    private $parser;

    /**
     * Basic setup
     */
    public function setUp(): void
    {
        $this->mock_function('sys_get_temp_dir', function () {
            return TEST_STATICS;
        });

        $this->parser = $this->getMockBuilder('\PHPDraft\In\ApibFileParser')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser->set_apib_content(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'));

        $this->class      = new DrafterAPI();
        $this->reflection = new ReflectionClass('PHPDraft\Parse\DrafterAPI');

        $this->class->init($this->parser);

        $this->unmock_function('sys_get_temp_dir');
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
     *
     * @covers \PHPDraft\Parse\DrafterAPI::parseToJson()
     */
    public function testSetupCorrectly(): void
    {
        $this->assertInstanceOf('\PHPDraft\In\ApibFileParser', $this->get_reflection_property_value('apib'));
    }

    /**
     * Test if the drafter api can be used
     *
     * @covers \PHPDraft\Parse\DrafterAPI::parseToJson()
     */
    public function testAvailableFails(): void
    {
        $this->mock_function('curl_exec', function () {
            return "/some/dir/drafter\n";
        });
        $this->mock_function('curl_errno', function () {
            return 1;
        });

        $this->assertFalse(DrafterAPI::available());

        $this->unmock_function('curl_errno');
        $this->unmock_function('curl_exec');
    }

    /**
     * Test if the drafter api can be used
     *
     * @covers \PHPDraft\Parse\DrafterAPI::parseToJson()
     */
    public function testAvailableSuccess(): void
    {
        $this->mock_function('curl_exec', function () {
            return "/some/dir/drafter\n";
        });
        $this->mock_function('curl_errno', function () {
            return 0;
        });

        $this->assertFalse(DrafterAPI::available());

        $this->unmock_function('curl_errno');
        $this->unmock_function('curl_exec');
    }

    /**
     * Check if the JSON is empty before parsing
     *
     * @covers \PHPDraft\Parse\DrafterAPI::parseToJson()
     */
    public function testPreRunStringIsEmpty(): void
    {
        $this->assertEmpty($this->class->json);
    }

    /**
     * Check if parsing the fails without drafter
     *
     * @covers \PHPDraft\Parse\DrafterAPI::parseToJson()
     */
    public function testParseWithFailingWebservice(): void
    {
        $this->expectException('\PHPDraft\Parse\ResourceException');
        $this->expectExceptionMessage('Drafter webservice failed to parse input');
        $this->expectExceptionCode(1);

        $this->mock_function('curl_errno', function () {
            return 1;
        });
        $this->class->parseToJson();
        $this->unmock_function('curl_errno');
    }

    /**
     * Check if parsing the succeeds
     *
     * @covers \PHPDraft\Parse\DrafterAPI::parseToJson()
     */
    public function testParseSuccess(): void
    {
        $this->mock_function('json_last_error', function () {
            return 0;
        });
        $this->mock_function('curl_errno', function () {
            return 0;
        });
        $this->mock_function('curl_exec', function () {
            return '{"content":[{"element":"world"}]}';
        });
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
