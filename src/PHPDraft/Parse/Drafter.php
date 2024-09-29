<?php

declare(strict_types=1);

/**
 * This file contains the Drafter.php.
 *
 * @package PHPDraft\Parse
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;

use PHPDraft\In\ApibFileParser;
use RuntimeException;
use UnexpectedValueException;

class Drafter extends BaseParser
{
    /**
     * The location of the drafter executable.
     *
     * @var string
     */
    protected string $drafter;

    /**
     * ApibToJson constructor.
     *
     * @param ApibFileParser $apib API Blueprint text
     *
     * @return BaseParser
     */
    public function init(ApibFileParser $apib): BaseParser
    {
        parent::init($apib);
        $loc = self::location();
        if ($loc === false) {
            throw new UnexpectedValueException("Could not find drafter location!");
        }
        $this->drafter = $loc;

        return $this;
    }

    /**
     * Return drafter location if found.
     *
     * @return false|string
     */
    public static function location(): false|string
    {
        $returnVal = shell_exec('which drafter 2> /dev/null');
        $returnVal = preg_replace('/^\s+|\n|\r|\s+$/m', '', $returnVal);

        return $returnVal === null || $returnVal === '' ? false : $returnVal;
    }

    /**
     * Parses the apib for the selected method.
     *
     * @return void
     */
    protected function parse(): void
    {
        shell_exec("$this->drafter $this->tmp_dir/index.apib -f json -o $this->tmp_dir/index.json 2> /dev/null");
        $content = file_get_contents($this->tmp_dir . '/index.json');
        if (!is_string($content)) {
            throw new RuntimeException('Could not read intermediary APIB file!');
        }

        $this->json = json_decode($content);
    }

    /**
     * Check if a given parser is available.
     *
     * @return bool
     */
    public static function available(): bool
    {
        $path = self::location();

        $version = shell_exec('drafter -v 2> /dev/null');
        $version = preg_match('/^v([45])/', $version);

        return $path && $version === 1;
    }
}
