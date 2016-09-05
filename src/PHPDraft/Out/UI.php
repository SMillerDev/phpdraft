<?php
/**
 * This file contains the UI.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out;


class UI
{
    static function main($argv = [])
    {
        $options = getopt("f:t::i::hv");
        if(!isset($argv[1]))
        {
            file_put_contents('php://stderr', 'Not enough arguments'.PHP_EOL);
            self::help();
            exit(1);
        }

        if (boolval(preg_match('/^\-/',$argv[1])))
        {
            if (isset($options['h']))
            {
                self::help();
                exit(0);
            }

            if (isset($options['v']))
            {
                self::version();
                exit(0);
            }

            elseif (isset($options['f']))
            {
                $file = $options['f'];
            }
            else
            {
                file_put_contents('php://stderr', 'No file to parse'.PHP_EOL);
                exit(1);
            }
        }
        else
        {
            $file = $argv[1];
        }

        $template = (isset($options['t']) && $options['t']) ? $options['t']: 'default';
        $image = (isset($options['i']) && $options['i']) ? $options['i']: NULL;

        return [
            'file' => $file,
            'template' => $template,
            'image' => $image
        ];
    }

    static function help()
    {
        echo 'This is a parser for API Blueprint files in PHP.'.PHP_EOL.PHP_EOL;
        echo "The following options can be used:.\n";
        echo "\t-f\tSpecifies the file to parse.\n";
        echo "\t-t\tSpecifies the template to use. (defaults to 'default')\n";
        echo "\t-h\tDisplays this text.\n";
    }

    static function version()
    {
        $version = (VERSION === '0') ? @exec('git describe --tags 2>&1') : VERSION;
        echo 'PHPDraft: '.$version;
    }

}