<?php
/**
 * This file contains the UI.php
 *
 * @package PHPDraft\Out
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out;

/**
 * Class UI
 */
class UI
{
    /**
     * Sets sorting to all parts
     *
     * @var int
     */
    public static $PHPD_SORT_ALL = 3;

    /**
     * Sets sorting to all webservices
     *
     * @var int
     */
    public static $PHPD_SORT_WEBSERVICES = 2;

    /**
     * Sets sorting to all data structures
     * @var int
     */
    public static $PHPD_SORT_STRUCTURES = 1;

    /**
     * Serve all options
     *
     * @param array $argv Arguments passed
     *
     * @return array results of the invocation
     */
    public static function main($argv = [])
    {
        $options = getopt('f:t:i:c:j:s:hvuyo');

        if (!isset($argv[1])) {
            file_put_contents('php://stderr', 'Not enough arguments' . PHP_EOL);
            self::help();
            throw new \RuntimeException('', 1);
        }

        $sorting = -1;
        if (isset($options['s'])) {
            $value = strtoupper($options['s']);
            if (isset(UI::${'PHPD_SORT_' . $value})) {
                $sorting = UI::${'PHPD_SORT_' . $value};
            }
        }

        if (boolval(preg_match('/^\-/', $argv[1]))) {
            if (isset($options['h'])) {
                self::help();
                throw new \RuntimeException('', 0);
            }

            if (isset($options['v'])) {
                self::version();
                throw new \RuntimeException('', 0);
            }

            if (isset($options['f'])) {
                $file = $options['f'];
            } else {
                throw new \RuntimeException('No file to parse', 1);
            }
        } else {
            $file = $argv[1];
        }
        if (isset($options['y']) || isset($options['o'])) {
            define('DRAFTER_ONLINE_MODE', 1);
        }

        $template = (isset($options['t']) && $options['t']) ? $options['t'] : 'default';
        $image    = (isset($options['i']) && $options['i']) ? $options['i'] : null;
        $css      = (isset($options['c']) && $options['c']) ? $options['i'] : null;
        $js       = (isset($options['j']) && $options['j']) ? $options['i'] : null;

        return [
            'file'     => $file,
            'template' => $template,
            'image'    => $image,
            'css'      => $css,
            'js'       => $js,
            'sorting'  => $sorting,
        ];
    }

    /**
     * Provide help
     *
     * @return void
     */
    public function help()
    {
        echo 'This is a parser for API Blueprint files in PHP.' . PHP_EOL . PHP_EOL;
        echo "The following options can be used:.\n";
        echo "\t-f\tSpecifies the file to parse.\n";
        echo "\t-t\tSpecifies the template to use. (defaults to 'default')\n";
        echo "\t-s\tSort displayed values [All|None|Structures|Webservices] (defaults to the way the objects are in the file)\n";
        echo "\t-i\tSpecifies an image to display in the header.\n";
        echo "\t-c\tSpecifies a CSS file to include (value is put in a link element without checking).\n";
        echo "\t-j\tSpecifies a JS file to include (value is put in a script element without checking).\n";
        echo "\t-h\tDisplays this text.\n";
    }

    /**
     * Return the version
     *
     * @return void
     */
    public function version()
    {
        $version = self::release_id();
        echo 'PHPDraft: ' . $version;
    }

    /**
     * Get the version number
     *
     * @return string
     */
    public function release_id()
    {
        return (VERSION === '0') ? @exec('git describe --tags 2>&1') : VERSION;
    }

    /**
     * Print the series of the update
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
     * Get the manner of releasing
     *
     * @return string
     */
    public function getReleaseChannel()
    {
        if (strpos(self::release_id(), '-') !== false) {
            return '-nightly';
        }

        return '';
    }

    /**
     * Ask a question to the user
     *
     * @param string $message  The question
     * @param array  $options  Possible answers
     *
     * @param string $positive The parameter that gives a positive outcome
     *
     * @return boolean
     */
    public static function ask($message, $options, $positive = 'y')
    {
        file_put_contents('php://stdout', $message);
        do {
            $selection = fgetc(STDIN);
        } while (trim($selection) == '');

        if (array_key_exists(strtolower($selection), $options)) {
            return ($selection === $positive);
        }
        if (array_search($selection, $options)) {
            return (array_search($selection, $options) === $positive);
        }
        file_put_contents('php://stderr', 'That answer wasn\'t expected, try again.'.PHP_EOL.PHP_EOL);

        return UI::ask($message, $options, $positive);
    }
}