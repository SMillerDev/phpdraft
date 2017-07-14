<?php
/**
 * This file contains the Drafter.php
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;

class Drafter extends BaseParser
{
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
        parent::__construct($apib);

        if (!$this->location())
        {
            throw new \RuntimeException('Drafter was not installed!', 1);
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
     * Parses the apib for the selected method
     *
     * @return void
     */
    protected function parse()
    {
        shell_exec($this->drafter . ' ' . $this->tmp_dir . '/index.apib -f json -o ' . $this->tmp_dir . '/index.json 2> /dev/null');
        $this->json = json_decode(file_get_contents($this->tmp_dir . '/index.json'));
    }
}