<?php
/**
 * This file contains the Drafter.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;

class Drafter
{
    /**
     * The API Blueprint output (JSON)
     *
     * @var string
     */
    public $json;
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
     * The location of the drafter executable
     *
     * @var string
     */
    protected $drafter;

    /**
     * ApibToJson constructor.
     *
     * @param string $apib API Blueprint text
     */
    public function __construct($apib)
    {
        global $config;
        $this->config = &$config;
        $this->apib   = $apib;

        if (!$this->location())
        {
            throw new \RuntimeException("Drafter was not installed!", 1);
        }
        $this->drafter = $this->location();
    }

    /**
     * Return drafter location if found
     *
     * @return bool|string
     */
    function location()
    {
        $returnVal = shell_exec('which drafter 2> /dev/null');
        $returnVal = preg_replace('/^\s+|\n|\r|\s+$/m', '', $returnVal);

        return (empty($returnVal) ? FALSE : $returnVal);
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

        shell_exec($this->drafter . ' ' . $tmp_dir . '/index.apib -f json -o ' . $tmp_dir . '/index.json 2> /dev/null');
        $this->json = json_decode(file_get_contents($tmp_dir . '/index.json'));

        if (json_last_error() !== JSON_ERROR_NONE)
        {
            file_put_contents('php://stdout', "ERROR: invalid json in " . $tmp_dir . '/index.json');
            throw new \RuntimeException("Drafter generated invalid JSON (" . json_last_error_msg() . ")", 2);
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
            throw new \RuntimeException("Parsing encountered errors and stopped", 2);
        }

        return $this->json;
    }

}