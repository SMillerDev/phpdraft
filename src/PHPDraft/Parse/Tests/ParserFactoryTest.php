<?php

namespace PHPDraft\Parse\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Parse\ParserFactory;

/**
 * Class ParserFactoryTest
 * @covers \PHPDraft\Parse\ParserFactory
 */
class ParserFactoryTest extends LunrBaseTest
{
    /**
     * @covers \PHPDraft\Parse\ParserFactory::getDrafter
     */
    public function testGetDrafter(): void
    {
        $this->mock_method(['\PHPDraft\Parse\Drafter', 'available'], fn() => true);

        $this->assertInstanceOf('\PHPDraft\Parse\Drafter', ParserFactory::getDrafter());

        $this->unmock_method(['\PHPDraft\Parse\Drafter', 'available']);
    }

    /**
     * @covers \PHPDraft\Parse\ParserFactory::getDrafter
     */
    public function testGetDrafterAPI(): void
    {
        $this->mock_method(['\PHPDraft\Parse\Drafter', 'available'], fn() => false);
        $this->mock_method(['\PHPDraft\Parse\DrafterAPI', 'available'], fn() => true);

        $this->assertInstanceOf('\PHPDraft\Parse\DrafterAPI', ParserFactory::getDrafter());

        $this->unmock_method(['\PHPDraft\Parse\Drafter', 'available']);
        $this->unmock_method(['\PHPDraft\Parse\DrafterAPI', 'available']);
    }

    /**
     * @covers \PHPDraft\Parse\ParserFactory::getDrafter
     */
    public function testGetDrafterFails(): void
    {
        $this->expectException('\PHPDraft\Parse\ResourceException');
        $this->expectExceptionMessage('Couldn\'t get an APIB parser');
        $this->mock_method(['\PHPDraft\Parse\Drafter', 'available'], fn() => false);
        $this->mock_method(['\PHPDraft\Parse\DrafterAPI', 'available'], fn() => false);

        ParserFactory::getDrafter();

        $this->unmock_method(['\PHPDraft\Parse\Drafter', 'available']);
        $this->unmock_method(['\PHPDraft\Parse\DrafterAPI', 'available']);
    }

    /**
     * @covers \PHPDraft\Parse\ParserFactory::getJson
     */
    public function testGetJson(): void
    {
        $this->mock_method(['\PHPDraft\Parse\Drafter', 'available'], fn() => false);
        $this->mock_method(['\PHPDraft\Parse\DrafterAPI', 'available'], fn() => true);

        $this->assertInstanceOf('\PHPDraft\Parse\HtmlGenerator', ParserFactory::getJson());

        $this->unmock_method(['\PHPDraft\Parse\Drafter', 'available']);
        $this->unmock_method(['\PHPDraft\Parse\DrafterAPI', 'available']);
    }

    /**
     * @covers \PHPDraft\Parse\ParserFactory::getJson
     */
    public function testGetJsonFails(): void
    {
        $this->expectException('\PHPDraft\Parse\ResourceException');
        $this->expectExceptionMessage('Couldn\'t get a JSON parser');
        $this->mock_method(['\PHPDraft\Parse\Drafter', 'available'], fn() => false);
        $this->mock_method(['\PHPDraft\Parse\DrafterAPI', 'available'], fn() => false);

        ParserFactory::getJson();

        $this->unmock_method(['\PHPDraft\Parse\Drafter', 'available']);
        $this->unmock_method(['\PHPDraft\Parse\DrafterAPI', 'available']);
    }

    /**
     * @covers \PHPDraft\Parse\ParserFactory::getOpenAPI
     */
    public function testGetOpenAPI(): void
    {
        $this->mock_method(['\PHPDraft\Parse\Drafter', 'available'], fn() => false);
        $this->mock_method(['\PHPDraft\Parse\DrafterAPI', 'available'], fn() => true);

        $this->assertInstanceOf('\PHPDraft\Out\OpenAPI\OpenApiRenderer', ParserFactory::getOpenAPI());

        $this->unmock_method(['\PHPDraft\Parse\Drafter', 'available']);
        $this->unmock_method(['\PHPDraft\Parse\DrafterAPI', 'available']);
    }

    /**
     * @covers \PHPDraft\Parse\ParserFactory::getOpenAPI
     */
    public function testGetOpenAPIFails(): void
    {
        $this->expectException('\PHPDraft\Parse\ResourceException');
        $this->expectExceptionMessage('Couldn\'t get an OpenAPI renderer');
        $this->mock_method(['\PHPDraft\Parse\Drafter', 'available'], fn() => false);
        $this->mock_method(['\PHPDraft\Parse\DrafterAPI', 'available'], fn() => false);

        ParserFactory::getOpenAPI();

        $this->unmock_method(['\PHPDraft\Parse\Drafter', 'available']);
        $this->unmock_method(['\PHPDraft\Parse\DrafterAPI', 'available']);
    }
}
