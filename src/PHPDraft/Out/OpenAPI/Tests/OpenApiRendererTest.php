<?php
namespace PHPDraft\Out\OpenAPI\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Model\HTTPRequest;
use PHPDraft\Out\OpenAPI\OpenApiRenderer;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(OpenApiRenderer::class)]
class OpenApiRendererTest extends LunrBaseTestCase
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
        $method = $this->getReflectionMethod('getTags');
        $result = $method->invokeArgs($this->class, []);

        $this->assertArrayEmpty($result);
    }

    public function testGetSecurity(): void
    {
        $method = $this->getReflectionMethod('getSecurity');
        $result = $method->invokeArgs($this->class, []);

        $this->assertArrayEmpty($result);
    }

    public function testGetComponents(): void
    {
        $method = $this->getReflectionMethod('getComponents');
        $result = $method->invokeArgs($this->class, []);

        $this->assertEquals((object)['schemas' => []], $result);
    }

    public function testGetDocs(): void
    {
        $this->markTestSkipped('Not implemented');

        $method = $this->getReflectionMethod('getDocs');
        $result = $method->invokeArgs($this->class, []);

        $this->assertEquals((object)[], $result);
    }

    public function testGetPaths(): void
    {
        $method = $this->getReflectionMethod('getPaths');
        $result = $method->invokeArgs($this->class, []);

        $this->assertEquals((object)[], $result);
    }

    public function testGetServers(): void
    {
        $method = $this->getReflectionMethod('getServers');
        $result = $method->invokeArgs($this->class, []);

        $this->assertEquals([['url' => null,'description' => 'Main host'], ['url' => '']], $result);
    }

    public function testGetApiInfo(): void
    {
        $method = $this->getReflectionMethod('getApiInfo');
        $result = $method->invokeArgs($this->class, []);

        $this->assertEquals([
                                    'title' => null,
                                    'version' => '1.0.0',
                                    'summary' => ' generated from API Blueprint',
                                    'description' => null,
                            ], $result);
    }

    public function testToResponses(): void
    {
        $method = $this->getReflectionMethod('toResponses');
        $result = $method->invokeArgs($this->class, [[]]);

        $this->assertEquals([], $result);
    }

    public function testToBody(): void
    {
        $mock = $this->getMockBuilder(HttpRequest::class)
                     ->disableOriginalConstructor()
                     ->getMock();

        $method = $this->getReflectionMethod('toBody');
        $result = $method->invokeArgs($this->class, [$mock]);

        $this->assertEquals([], $result);
    }

    public function testToParameters(): void
    {
        $method = $this->getReflectionMethod('toParameters');
        $result = $method->invokeArgs($this->class, [[], 'href']);

        $this->assertEquals([], $result);
    }
}
