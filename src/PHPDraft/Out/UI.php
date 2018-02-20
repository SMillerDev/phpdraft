<?php
/**
 * This file contains the UI.php.
 *
 * @package PHPDraft\Out
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out;

use PHPDraft\Parse\ExecutionException;

/**
 * Class UI.
 */
class UI
{
    /**
     * Sets sorting to all parts.
     *
     * @var int
     */
    public static $PHPD_SORT_ALL = 3;

    /**
     * Sets sorting to all webservices.
     *
     * @var int
     */
    public static $PHPD_SORT_WEBSERVICES = 2;

    /**
     * Sets sorting to all data structures.
     *
     * @var int
     */
    public static $PHPD_SORT_STRUCTURES = 1;

    /**
     * Serve all options.
     *
     * @param array $argv Arguments passed
     *
     * @return array results of the invocation
     * @throws \PHPDraft\Parse\ExecutionException When something failed in execution
     */
    public static function main($argv = [])
    {
        $options = getopt('f:t:i:c:j:s:hvyo');

        if (!isset($argv[1])) {
            file_put_contents('php://stderr', 'Not enough arguments' . PHP_EOL);
            (new self())->help();

            throw new ExecutionException('', 1);
        }

        $sorting = -1;
        if (isset($options['s'])) {
            $value = strtoupper($options['s']);
            if (isset(self::${'PHPD_SORT_' . $value})) {
                $sorting = self::${'PHPD_SORT_' . $value};
            }
        }

        if (boolval(preg_match('/^\-/', $argv[1]))) {
            if (isset($options['h'])) {
                (new self())->help();

                throw new ExecutionException('', 0);
            }

            if (isset($options['v'])) {
                (new self())->version();

                throw new ExecutionException('', 0);
            }

            if (isset($options['f'])) {
                $file = $options['f'];
            } else {
                throw new ExecutionException('No file to parse', 1);
            }
        } else {
            $file = $argv[1];
        }
        define('THIRD_PARTY_ALLOWED', getenv('PHPDRAFT_THIRD_PARTY') !== '0');
        if ((isset($options['y']) || isset($options['o'])) && THIRD_PARTY_ALLOWED) {
            define('DRAFTER_ONLINE_MODE', 1);
        }

        $template = (new self())->var_or_default($options['t'], 'default');
        $image    = (new self())->var_or_default($options['i']);
        $css      = (new self())->var_or_default($options['c']);
        $js       = (new self())->var_or_default($options['j']);
        $color1   = getenv('COLOR_PRIMARY') === FALSE ? NULL : getenv('COLOR_PRIMARY');
        $color1   = (new self())->var_or_default($color1);
        $color2   = getenv('COLOR_SECONDARY') === FALSE ? NULL : getenv('COLOR_SECONDARY');
        $color2   = (new self())->var_or_default($color2);
        $colors   = (is_null($color1) || is_null($color2)) ? '' : '__' . $color1 . '__' . $color2;

        return [
            'file'     => $file,
            'template' => $template . $colors,
            'image'    => $image,
            'css'      => $css,
            'js'       => $js,
            'sorting'  => $sorting,
        ];
    }

    /**
     * Provide help.
     *
     * @return void
     */
    public function help()
    {
        echo 'This is a parser for API Blueprint files in PHP.' . PHP_EOL . PHP_EOL;
        echo 'The following options can be used:.' . PHP_EOL;
        echo "\t-f\tSpecifies the file to parse." . PHP_EOL;
        echo "\t-y\tAlways accept using the online mode." . PHP_EOL;
        echo "\t-o\tAlways use the online mode." . PHP_EOL;
        echo "\t-t\tSpecifies the template to use. (defaults to 'default')" . PHP_EOL;
        echo "\t-s\tSort displayed values [All|None|Structures|Webservices] (defaults to the way the objects are in the file)" . PHP_EOL;
        echo "\t-i\tSpecifies an image to display in the header." . PHP_EOL;
        echo "\t-c\tSpecifies a CSS file to include (value is put in a link element without checking)." . PHP_EOL;
        echo "\t-j\tSpecifies a JS file to include (value is put in a script element without checking)." . PHP_EOL;
        echo "\t-v\tPrint the version for PHPDraft." . PHP_EOL;
        echo "\t-h\tDisplays this text." . PHP_EOL;
    }

    /**
     * Check if a variable exists, otherwise return a default.
     *
     * @param mixed $var
     * @param mixed $default
     *
     * @return mixed
     */
    private function var_or_default(&$var, $default = NULL)
    {
        if (!isset($var) || is_null($var)) {
            return $default;
        }

        return $var;
    }

    /**
     * Return the version.
     *
     * @return void
     */
    public function version()
    {
        $version = self::release_id();
        echo 'PHPDraft: ' . $version;
    }

    /**
     * Get the version number.
     *
     * @return string
     */
    public function release_id()
    {
        return (VERSION === '0') ? @exec('git describe --tags 2>&1') : VERSION;
    }

    /**
     * Print the series of the update.
     *
     * @return string Series
     */
    public function series()
    {
        if (strpos(self::release_id(), '-')) {
            $version = explode('-', self::release_id())[0];
        } else {
            $version = self::release_id();
        }

        return implode('.', array_slice(explode('.', $version), 0, 2));
    }

    /**
     * Get the manner of releasing.
     *
     * @return string
     */
    public function getReleaseChannel()
    {
        if (strpos(self::release_id(), '-') !== FALSE) {
            return '-nightly';
        }

        return '';
    }

    /**
     * Ask a question to the user.
     *
     * @param string $message  The question
     * @param array  $options  Possible answers
     * @param string $positive The parameter that gives a positive outcome
     *
     * @return bool
     */
    public static function ask($message, $options, $positive = 'y')
    {
        file_put_contents('php://stdout', $message);
        do {
            $selection = fgetc(STDIN);
        } while (trim($selection) == '');

        if (array_key_exists(strtolower($selection), $options)) {
            return $selection === $positive;
        }
        if (array_search($selection, $options)) {
            return array_search($selection, $options) === $positive;
        }
        file_put_contents('php://stderr', 'That answer wasn\'t expected, try again.' . PHP_EOL . PHP_EOL);

        return self::ask($message, $options, $positive);
    }
}
