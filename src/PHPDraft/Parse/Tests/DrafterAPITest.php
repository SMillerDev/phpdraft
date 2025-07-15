<?php

/**
 * This file contains the DrafterAPITest.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\In\ApibFileParser;
use PHPDraft\Parse\DrafterAPI;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class DrafterAPITest
 */
#[CoversClass(DrafterAPI::class)]
class DrafterAPITest extends LunrBaseTestCase
{
    /**
     * Shared instance of the file parser.
     *
     * @var ApibFileParser&MockObject
     */
    private ApibFileParser&MockObject $parser;

    private DrafterAPI $class;

    /**
     * Basic setup
     */
    public function setUp(): void
    {
        $this->mockFunction('sys_get_temp_dir', fn() => TEST_STATICS);

        $this->parser = $this->getMockBuilder('\PHPDraft\In\ApibFileParser')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser->set_apib_content(file_get_contents(TEST_STATICS . '/drafter/apib/index.apib'));

        $this->class      = new DrafterAPI();
        $this->baseSetUp($this->class);

        $this->class->init($this->parser);

        $this->unmockFunction('sys_get_temp_dir');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
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
     * Test if the drafter api can be used
     */
    public function testAvailableFails(): void
    {
        $this->mockFunction('curl_exec', fn() => "/some/dir/drafter\n");
        $this->mockFunction('curl_errno', fn() => 1);

        $this->assertFalse(DrafterAPI::available());

        $this->unmockFunction('curl_errno');
        $this->unmockFunction('curl_exec');
    }

    /**
     * Test if the drafter api can be used
     */
    public function testAvailableSuccess(): void
    {
        $this->mockFunction('curl_exec', fn() => "/some/dir/drafter\n");
        $this->mockFunction('curl_errno', fn() => 0);

        $this->assertFalse(DrafterAPI::available());

        $this->unmockFunction('curl_errno');
        $this->unmockFunction('curl_exec');
    }

    /**
     * Check if parsing the fails without drafter
     */
    public function testParseWithFailingWebservice(): void
    {
        $this->expectException('\PHPDraft\Parse\ResourceException');
        $this->expectExceptionMessage('Drafter webservice failed to parse input');
        $this->expectExceptionCode(1);

        $this->mockFunction('curl_errno', fn() => 1);
        $this->class->parseToJson();
        $this->unmockFunction('curl_errno');
    }

    /**
     * Check if parsing the succeeds
     */
    public function testParseSuccess(): void
    {
        $this->mockFunction('json_last_error', fn() => 0);
        $this->mockFunction('curl_errno', fn() => 0);
        $this->mockFunction('curl_exec', fn() => '{"content":[{"element":"world"}]}');

        $this->class->parseToJson();

        $this->unmockFunction('curl_exec');
        $this->unmockFunction('curl_errno');
        $this->unmockFunction('json_last_error');

        $obj           = (object)[];
        $obj2          = (object)[];
        $obj2->element = 'world';
        $obj->content  = [ $obj2 ];
        $this->assertEquals($obj, $this->class->json);
    }
}
