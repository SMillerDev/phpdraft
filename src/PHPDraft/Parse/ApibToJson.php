<?php
/**
 * This file contains the ApibToJson.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;

class ApibToJson
{
    /**
     * Configuration
     *
     * @var array
     */
    protected $config;

    /**
     * The API Blueprint input
     *
     * @var string
     */
    protected $apib;

    /**
     * The API Blueprint output (JSON)
     *
     * @var string
     */
    public $json;

    /**
     * ApibToJson constructor.
     *
     * @param string $apib API Blueprint text
     */
    public function __construct($apib)
    {
        global $config;
        $this->config = $config;
        $this->apib   = $apib;
    }

    /**
     * Parse the API Blueprint text to JSON
     *
     * @return string API Blueprint text
     */
    public function parseToJson()
    {
        $tmp_dir = $this->config->tmpdir;

        if (!file_exists($tmp_dir))
        {
            mkdir($tmp_dir);
        }

        file_put_contents($tmp_dir . '/index.apib', $this->apib);
        if (!$this->drafter_location())
        {
            file_put_contents('php://stderr', "Drafter was not installed!\n");
            exit(1);
        }

        shell_exec($this->drafter_location() . ' ' . $tmp_dir . '/index.apib -f json -o ' . $tmp_dir . '/index.json 2> /dev/null');
        $this->json = json_decode(file_get_contents($tmp_dir . '/index.json'));
        if (json_last_error() !== JSON_ERROR_NONE)
        {
            file_put_contents('php://stderr', "Drafter generated invalid JSON!\n" . json_last_error_msg() . "\n");
            file_put_contents('php://stdout', file_get_contents($tmp_dir . '/index.json') . "\n");
            exit(2);
        }
        $warnings = FALSE;
        foreach ($this->json->content as $item)
        {
            if ($item->element === 'annotation')
            {
                $warnings = TRUE;
                $prefix   = strtoupper($item->meta->classes[0]);
                $error    = $item->content;
                file_put_contents('php://stdout', "$prefix: $error\n");
            }
        }
        if ($warnings)
        {
            file_put_contents('php://stderr', "Parsing encountered errors and stopped\n");
            exit(2);
        }

        return $this->json;
    }

    /**
     * Return drafter location if found
     *
     * @return bool|string
     */
    function drafter_location()
    {
        $returnVal = shell_exec('which drafter 2> /dev/null');
        $returnVal = preg_replace('/^\s+|\n|\r|\s+$/m', '', $returnVal);

        return (empty($returnVal) ? FALSE : $returnVal);
    }

}