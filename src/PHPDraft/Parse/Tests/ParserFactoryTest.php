<?php

namespace PHPDraft\Parse\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Out\OpenAPI\OpenApiRenderer;
use PHPDraft\Parse\Drafter;
use PHPDraft\Parse\DrafterAPI;
use PHPDraft\Parse\HtmlGenerator;
use PHPDraft\Parse\ParserFactory;
use PHPDraft\Parse\ResourceException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class ParserFactoryTest
 */
#[CoversClass(ParserFactory::class)]
class ParserFactoryTest extends LunrBaseTestCase
{
    public function testGetDrafter(): void
    {
        $this->mockMethod([Drafter::class, 'available'], fn() => true);

        $this->assertInstanceOf(Drafter::class, ParserFactory::getDrafter());

        $this->unmockMethod([Drafter::class, 'available']);
    }

    public function testGetDrafterAPI(): void
    {
        $this->mockMethod([Drafter::class, 'available'], fn() => false);
        $this->mockMethod([DrafterApi::class, 'available'], fn() => true);

        $this->assertInstanceOf(DrafterApi::class, ParserFactory::getDrafter());

        $this->unmockMethod([Drafter::class, 'available']);
        $this->unmockMethod([DrafterApi::class, 'available']);
    }

    public function testGetDrafterFails(): void
    {
        $this->expectException('\PHPDraft\Parse\ResourceException');
        $this->expectExceptionMessage('Couldn\'t get an APIB parser');
        $this->mockMethod([Drafter::class, 'available'], fn() => false);
        $this->mockMethod([DrafterApi::class, 'available'], fn() => false);

        ParserFactory::getDrafter();

        $this->unmockMethod([Drafter::class, 'available']);
        $this->unmockMethod([DrafterApi::class, 'available']);
    }

    public function testGetJson(): void
    {
        $this->mockMethod([Drafter::class, 'available'], fn() => false);
        $this->mockMethod([DrafterApi::class, 'available'], fn() => true);

        $this->assertInstanceOf(HtmlGenerator::class, ParserFactory::getJson());

        $this->unmockMethod([Drafter::class, 'available']);
        $this->unmockMethod([DrafterApi::class, 'available']);
    }

    public function testGetJsonFails(): void
    {
        $this->expectException(ResourceException::class);
        $this->expectExceptionMessage('Couldn\'t get a JSON parser');

        $this->mockMethod([Drafter::class, 'available'], fn() => false);
        $this->mockMethod([DrafterApi::class, 'available'], fn() => false);

        ParserFactory::getJson();

        $this->unmockMethod([Drafter::class, 'available']);
        $this->unmockMethod([DrafterApi::class, 'available']);
    }

    public function testGetOpenAPI(): void
    {
        $this->mockMethod([Drafter::class, 'available'], fn() => false);
        $this->mockMethod([DrafterApi::class, 'available'], fn() => true);

        $this->assertInstanceOf(OpenApiRenderer::class, ParserFactory::getOpenAPI());

        $this->unmockMethod([Drafter::class, 'available']);
        $this->unmockMethod([DrafterApi::class, 'available']);
    }

    public function testGetOpenAPIFails(): void
    {
        $this->expectException(ResourceException::class);
        $this->expectExceptionMessage('Couldn\'t get an OpenAPI renderer');
        $this->mockMethod([Drafter::class, 'available'], fn() => false);
        $this->mockMethod([DrafterApi::class, 'available'], fn() => false);

        ParserFactory::getOpenAPI();

        $this->unmockMethod([Drafter::class, 'available']);
        $this->unmockMethod([DrafterApi::class, 'available']);
    }
}
