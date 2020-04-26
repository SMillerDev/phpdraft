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
    public function testGetDrafter()
    {
        $this->mock_method(['\PHPDraft\Parse\Drafter', 'available'], function () {
            return true;
        });

        $this->assertInstanceOf('\PHPDraft\Parse\Drafter', ParserFactory::getDrafter());

        $this->unmock_method(['\PHPDraft\Parse\Drafter', 'available']);
    }

    /**
     * @covers \PHPDraft\Parse\ParserFactory::getDrafter
     */
    public function testGetDrafterAPI()
    {
        $this->mock_method(['\PHPDraft\Parse\Drafter', 'available'], function () {
            return false;
        });
        $this->mock_method(['\PHPDraft\Parse\DrafterAPI', 'available'], function () {
            return true;
        });

        $this->assertInstanceOf('\PHPDraft\Parse\DrafterAPI', ParserFactory::getDrafter());

        $this->unmock_method(['\PHPDraft\Parse\Drafter', 'available']);
        $this->unmock_method(['\PHPDraft\Parse\DrafterAPI', 'available']);
    }

    /**
     * @covers \PHPDraft\Parse\ParserFactory::getDrafter
     */
    public function testGetDrafterFails()
    {
        $this->expectException('\PHPDraft\Parse\ResourceException');
        $this->expectExceptionMessage('Couldn\'t get an APIB parser');
        $this->mock_method(['\PHPDraft\Parse\Drafter', 'available'], function () {
            return false;
        });
        $this->mock_method(['\PHPDraft\Parse\DrafterAPI', 'available'], function () {
            return false;
        });

        ParserFactory::getDrafter();

        $this->unmock_method(['\PHPDraft\Parse\Drafter', 'available']);
        $this->unmock_method(['\PHPDraft\Parse\DrafterAPI', 'available']);
    }

    /**
     * @covers \PHPDraft\Parse\ParserFactory::getJson
     */
    public function testGetJson()
    {
        $this->mock_method(['\PHPDraft\Parse\Drafter', 'available'], function () {
            return false;
        });
        $this->mock_method(['\PHPDraft\Parse\DrafterAPI', 'available'], function () {
            return true;
        });

        $this->assertInstanceOf('\PHPDraft\Parse\HtmlGenerator', ParserFactory::getJson());

        $this->unmock_method(['\PHPDraft\Parse\Drafter', 'available']);
        $this->unmock_method(['\PHPDraft\Parse\DrafterAPI', 'available']);
    }

    /**
     * @covers \PHPDraft\Parse\ParserFactory::getJson
     */
    public function testGetJsonFails()
    {
        $this->expectException('\PHPDraft\Parse\ResourceException');
        $this->expectExceptionMessage('Couldn\'t get a JSON parser');
        $this->mock_method(['\PHPDraft\Parse\Drafter', 'available'], function () {
            return false;
        });
        $this->mock_method(['\PHPDraft\Parse\DrafterAPI', 'available'], function () {
            return false;
        });

        ParserFactory::getJson();

        $this->unmock_method(['\PHPDraft\Parse\Drafter', 'available']);
        $this->unmock_method(['\PHPDraft\Parse\DrafterAPI', 'available']);
    }
}
