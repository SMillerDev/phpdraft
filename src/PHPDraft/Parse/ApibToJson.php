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
    protected $json;

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
        if (!$this->drafter_location()) {
            file_put_contents('php://stderr', "Drafter was not installed!\n");
            exit(1);
        }

        shell_exec($this->drafter_location(). ' ' . $tmp_dir . '/index.apib -f json -o ' . $tmp_dir . '/index.json 2> /dev/null');
        $this->json = file_get_contents($tmp_dir . '/index.json');
        return $this->apib;
    }

    /**
     * Return drafter location if found
     *
     * @return bool|string
     */
    function drafter_location() {
        $returnVal = shell_exec('which drafter 2> /dev/null');
        $returnVal = preg_replace('/^\s+|\n|\r|\s+$/m', '', $returnVal);
        return (empty($returnVal) ? FALSE : $returnVal);
    }

    /**
     * JSON representation
     *
     * @return string
     */
    function __toString()
    {
        return $this->json;
    }

}