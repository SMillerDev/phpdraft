<?php

declare(strict_types=1);

namespace PHPDraft\Parse;

use PHPDraft\Out\OpenAPI\OpenApiRenderer;

/**
 * Class ParserFactory.
 */
class ParserFactory
{
    /**
     * Get the applicable Drafter parser.
     *
     * @return BaseParser The parser that can be used
     */
    public static function getDrafter(): BaseParser
    {
        if (Drafter::available()) {
            return new Drafter();
        }
        if (DrafterAPI::available()) {
            return new DrafterAPI();
        }

        throw new ResourceException("Couldn't get an APIB parser", 255);
    }

    /**
     * Get the applicable JSON parser.
     *
     * @return BaseHtmlGenerator The parser that can be used
     */
    public static function getJson(): BaseHtmlGenerator
    {
        if (Drafter::available() || DrafterAPI::available()) {
            return new HtmlGenerator();
        }

        throw new ResourceException("Couldn't get a JSON parser", 255);
    }

    public static function getOpenAPI(): OpenApiRenderer
    {
        if (Drafter::available() || DrafterAPI::available()) {
            return new OpenApiRenderer();
        }

        throw new ResourceException("Couldn't get an OpenAPI renderer", 255);
    }
}
