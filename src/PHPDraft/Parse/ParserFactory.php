<?php

namespace PHPDraft\Parse;

/**
 * Class ParserFactory.
 */
class ParserFactory
{
    /**
     * Get the applicable parser.
     *
     * @return \PHPDraft\Parse\BaseParser The parser that can be used
     */
    public static function get(): BaseParser
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
}
