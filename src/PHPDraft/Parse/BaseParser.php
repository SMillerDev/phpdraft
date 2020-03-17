<?php
declare(strict_types=1);

/**
 * This file contains the BaseParser.php.
 *
 * @package PHPDraft\Parse
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;

use PHPDraft\In\ApibFileParser;

/**
 * Class BaseParser.
 *
 * @package PHPDraft\Parse
 */
abstract class BaseParser
{
    /**
     * The API Blueprint output (JSON).
     *
     * @var object
     */
    public $json;

    /**
     * Temp directory.
     *
     * @var string
     */
    protected $tmp_dir;

    /**
     * The API Blueprint input.
     *
     * @var ApibFileParser
     */
    protected $apib;

    /**
     * BaseParser constructor.
     *
     * @param ApibFileParser $apib API Blueprint text
     *
     * @return \PHPDraft\Parse\BaseParser
     */
    public function init(ApibFileParser $apib): self
    {
        $this->apib    = $apib;
        $this->tmp_dir = sys_get_temp_dir() . '/drafter';

        return $this;
    }

    /**
     * BaseParser destructor.
     */
    public function __destruct()
    {
        unset($this->apib);
        unset($this->json);
        unset($this->tmp_dir);
    }

    /**
     * Parse the API Blueprint text to JSON.
     *
     * @throws ExecutionException When the JSON is invalid or warnings are thrown in parsing
     *
     * @return object JSON output.
     */
    public function parseToJson(): object
    {
        if (!file_exists($this->tmp_dir)) {
            mkdir($this->tmp_dir, 0777, true);
        }

        file_put_contents($this->tmp_dir . '/index.apib', $this->apib->content());

        $this->parse();

        if (json_last_error() !== JSON_ERROR_NONE) {
            file_put_contents('php://stderr', 'ERROR: invalid json in ' . $this->tmp_dir . '/index.json');

            throw new ExecutionException('Drafter generated invalid JSON (' . json_last_error_msg() . ')', 2);
        }

        $warnings = false;
        foreach ($this->json->content as $item) {
            if ($item->element === 'annotation') {
                $warnings = true;
                $line     = $item->attributes->sourceMap->content[0]->content[0]->content[0]->attributes->line->content ?? 'UNKNOWN';
                $prefix   = (is_array($item->meta->classes)) ? strtoupper($item->meta->classes[0]) : strtoupper($item->meta->classes->content[0]->content);
                $error    = $item->content;
                file_put_contents('php://stderr', "$prefix: $error (line $line)\n");
                file_put_contents('php://stdout', "<pre>$prefix: $error (line $line)</pre>\n");
            }
        }

        if ($warnings) {
            throw new ExecutionException('Parsing encountered errors and stopped', 2);
        }

        return $this->json;
    }

    /**
     * Parses the apib for the selected method.
     *
     * @return void
     */
    abstract protected function parse();

    /**
     * Check if a given parser is available.
     *
     * @return bool
     */
    abstract public static function available(): bool;
}
