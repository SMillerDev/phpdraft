<?php
declare(strict_types=1);

/**
 * This file contains the LegacyDrafter.php.
 *
 * @package PHPDraft\Parse
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;

use PHPDraft\In\ApibFileParser;

/**
 * Class LegacyDrafter
 *
 * @deprecated No longer supported
 */
class LegacyDrafter extends BaseParser
{
    /**
     * The location of the drafter executable.
     *
     * @var string
     */
    protected $drafter;

    /**
     * ApibToJson constructor.
     *
     * @param ApibFileParser $apib API Blueprint text
     *
     * @return \PHPDraft\Parse\BaseParser
     */
    public function init(ApibFileParser $apib): BaseParser
    {
        parent::init($apib);
        $this->drafter = self::location();

        return $this;
    }

    /**
     * Return drafter location if found.
     *
     * @return bool|string
     */
    public static function location()
    {
        return false;
    }

    /**
     * Check if a given parser is available.
     *
     * @deprecated V2 doesn't support drafter 3.
     *
     * @return bool
     */
    public static function available(): bool
    {
        return false;
    }

    /**
     * Parses the apib for the selected method.
     *
     * @return void
     */
    protected function parse(): void
    {
        return;
    }
}
