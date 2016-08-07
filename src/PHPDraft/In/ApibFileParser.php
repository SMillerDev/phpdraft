<?php
/**
 * This file contains the ApibFileParser
 *
 * @package PHPDraft\In
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\In;

class ApibFileParser
{
    protected $out_string;

    protected $location;

    /**
     * FileParser constructor.
     *
     * @param string $filename File to parse
     */
    public function __construct($filename = 'index.apib')
    {
        $this->location   = pathinfo($filename, PATHINFO_DIRNAME) . '/';
        $this->out_string = $this->get_apib($filename);
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
        $file    = file_get_contents($filename);
        $matches = [];
        preg_match_all('<!-- include\(([a-z_.]*?).apib\) -->', $file, $matches);
        foreach ($matches[1] as $value) {
            $file = str_replace('<!-- include(' . $value . '.apib) -->', $this->get_apib($this->location . $value . '.apib'), $file);
        }

        return $file;
    }

    /**
     * Return the value of the class
     *
     * @return string
     */
    function __toString()
    {
        return $this->out_string;
    }

}