<?php
/**
 * This file contains the ApibFileParser
 *
 * @package PHPDraft\In
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\In;

/**
 * Class ApibFileParser
 */
class ApibFileParser
{
    /**
     * Complete API Blueprint
     *
     * @var string
     */
    protected $full_apib;

    /**
     * Location of the API Blueprint to parse
     *
     * @var string
     */
    protected $location;

    /**
     * FileParser constructor.
     *
     * @param string $filename File to parse
     */
    public function __construct($filename = 'index.apib')
    {
        $this->location = pathinfo($filename, PATHINFO_DIRNAME) . '/';

        set_include_path(get_include_path() . ':' . $this->location);

        $this->full_apib = $this->get_apib($filename);
    }

    /**
     * Parse a given API Blueprint file
     * This changes all `include(file)` tags to the contents of the file
     *
     * @param string $filename File to parse
     *
     * @return string The full API blueprint file
     */
    function get_apib($filename)
    {
        $this->file_check($filename);
        $file    = file_get_contents($filename);
        $matches = [];
        preg_match_all('<!-- include\(([a-z0-9_.\/]*?).apib\) -->', $file, $matches);
        foreach ($matches[1] as $value) {
            $file = str_replace('<!-- include(' . $value . '.apib) -->',
                $this->get_apib($this->location . $value . '.apib'), $file);
        }

        preg_match_all('<!-- schema\(([a-z0-9_.\/\:]*?)\) -->', $file, $matches);
        foreach ($matches[1] as $value) {
            $file = str_replace('<!-- schema(' . $value . ') -->', $this->get_schema($value), $file);
        }

        return $file;
    }

    /**
     * Check if an APIB file exists
     *
     * @param string $filename File to check
     *
     * @return void
     */
    private function file_check($filename)
    {
        if (!file_exists($filename)) {
            file_put_contents('php://stderr', "API File not found: $filename\n");
            exit(1);
        }
    }

    /**
     * Get an external Schema by URL
     *
     * @param string $url URL to fetch the schema from
     *
     * @return string The schema as a string
     */
    function get_schema($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Return the value of the class
     *
     * @return string
     */
    function __toString()
    {
        return $this->full_apib;
    }

}