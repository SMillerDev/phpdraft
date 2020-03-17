<?php
declare(strict_types=1);

namespace PHPDraft\Parse;

/**
 * Class ParserFactory.
 */
class ParserFactory
{
    /**
     * Get the applicable Drafter parser.
     *
     * @return \PHPDraft\Parse\BaseParser The parser that can be used
     */
    public static function getDrafter(): BaseParser
    {
        if (Drafter::available()) {
            return new Drafter();
        }
        if (DrafterAPI::available()) {
            return new DrafterAPI();
        }
        if (LegacyDrafter::available()) {
            throw new ResourceException('Drafter 3.x is no longer supported', 100);
        }

        throw new ResourceException("Couldn't get an apib parser", 255);
    }

    /**
     * Get the applicable JSON parser.
     *
     * @return \PHPDraft\Parse\BaseHtmlGenerator The parser that can be used
     */
    public static function getJson(): BaseHtmlGenerator
    {
        if (Drafter::available() || DrafterAPI::available()) {
            return new HtmlGenerator();
        }
        if (LegacyDrafter::available()) {
            throw new ResourceException('Drafter 3.x is no longer supported', 100);
        }

        throw new ResourceException("Couldn't get a JSON parser", 255);
    }
}
