<?php
namespace PHPDraft\Out\OpenAPI\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\HTTPRequest;
use PHPDraft\Out\OpenAPI\OpenApiRenderer;

/**
 * @covers \PHPDraft\Out\OpenAPI\OpenApiRenderer
 */
class OpenApiRendererTest extends LunrBaseTest
{
    private OpenApiRenderer $class;

    /**
     * Set up tests
     */
    public function setUp(): void
    {
        $this->class = new OpenApiRenderer();
        $this->baseSetUp($this->class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testWrite(): void
    {
        $this->class->init((object)[]);

        $tmpfile = tempnam(sys_get_temp_dir(), 'fdsfds');
        $this->class->write($tmpfile);
        $this->assertFileEquals(TEST_STATICS . '/openapi/empty.json', $tmpfile);
    }

    public function testGetTags(): void
    {
        $method = $this->get_reflection_method('getTags');
        $result = $method->invokeArgs($this->class, []);

        $this->assertArrayEmpty($result);
    }

    public function testGetSecurity(): void
    {
        $method = $this->get_reflection_method('getSecurity');
        $result = $method->invokeArgs($this->class, []);

        $this->assertArrayEmpty($result);
    }

    public function testGetComponents(): void
    {
        $method = $this->get_reflection_method('getComponents');
        $result = $method->invokeArgs($this->class, []);

        $this->assertEquals((object)['schemas' => []],$result);
    }

    public function testGetDocs(): void
    {
        $this->markTestSkipped('Not implemented');

        $method = $this->get_reflection_method('getDocs');
        $result = $method->invokeArgs($this->class, []);

        $this->assertEquals((object)[],$result);
    }

    public function testGetPaths(): void
    {
        $method = $this->get_reflection_method('getPaths');
        $result = $method->invokeArgs($this->class, []);

        $this->assertEquals((object)[],$result);
    }

    public function testGetServers(): void
    {
        $method = $this->get_reflection_method('getServers');
        $result = $method->invokeArgs($this->class, []);

        $this->assertEquals([['url' => null,'description' => 'Main host'], ['url' => '']],$result);
    }

    public function testGetApiInfo(): void
    {
        $method = $this->get_reflection_method('getApiInfo');
        $result = $method->invokeArgs($this->class, []);

        $this->assertEquals([
                                    'title' => null,
                                    'version' => '1.0.0',
                                    'summary' => ' generated from API Blueprint',
                                    'description' => null,
                            ],$result);
    }

    public function testToResponses(): void
    {
        $method = $this->get_reflection_method('toResponses');
        $result = $method->invokeArgs($this->class, [[]]);

        $this->assertEquals([],$result);
    }

    public function testToBody(): void
    {
        $mock = $this->getMockBuilder(HttpRequest::class)
                     ->disableOriginalConstructor()
                     ->getMock();

        $method = $this->get_reflection_method('toBody');
        $result = $method->invokeArgs($this->class, [$mock]);

        $this->assertEquals(['content' => ['text/plain' => ['schema' => ['type' => 'string']]]],$result);
    }

    public function testToParameters(): void
    {
        $mock = $this->getMockBuilder(HttpRequest::class)
                     ->disableOriginalConstructor()
                     ->getMock();

        $method = $this->get_reflection_method('toParameters');
        $result = $method->invokeArgs($this->class, [[], 'href']);

        $this->assertEquals([],$result);
    }
}