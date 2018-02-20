<?php
/**
 * This file contains the BaseParser.php.
 *
 * @package PHPDraft\Parse
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;

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
     * @var string
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
     * @var string
     */
    protected $apib;

    /**
     * BaseParser constructor.
     *
     * @param string $apib API Blueprint text
     */
    public function __construct($apib)
    {
        $this->apib    = $apib;
        $this->tmp_dir = sys_get_temp_dir() . '/drafter';
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
     * @return string API Blueprint text
     */
    public function parseToJson()
    {
        if (!file_exists($this->tmp_dir)) {
            mkdir($this->tmp_dir, 0777, TRUE);
        }

        file_put_contents($this->tmp_dir . '/index.apib', $this->apib);

        $this->parse();

        if (json_last_error() !== JSON_ERROR_NONE) {
            file_put_contents('php://stderr', 'ERROR: invalid json in ' . $this->tmp_dir . '/index.json');

            throw new ExecutionException('Drafter generated invalid JSON (' . json_last_error_msg() . ')', 2);
        }

        $warnings = FALSE;
        foreach ($this->json->content as $item) {
            if ($item->element === 'annotation') {
                $warnings = TRUE;
                $prefix   = strtoupper($item->meta->classes[0]);
                $error    = $item->content;
                file_put_contents('php://stderr', "$prefix: $error\n");
                file_put_contents('php://stdout', "<pre>$prefix: $error</pre>\n");
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
}
