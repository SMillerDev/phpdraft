<?php

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
        if (LegacyDrafter::available()) {
            return new LegacyDrafter();
        }
        if (DrafterAPI::available()) {
            return new DrafterAPI();
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
        if (LegacyDrafter::available()) {
            return new LegacyHtmlGenerator();
        }
        if (Drafter::available() || DrafterAPI::available()) {
            return new HtmlGenerator();
        }

        throw new ResourceException("Couldn't get a JSON parser", 255);
    }
}
