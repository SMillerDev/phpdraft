<?php
/**
 * This file contains the UI.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out;


use Exception;
use Phar;
use Throwable;

class UI
{
    public static $PHPD_SORT_OPT_ALL = 3;
    public static $PHPD_SORT_OPT_WEBSERVICES = 2;
    public static $PHPD_SORT_OPT_STRUCTURES = 1;
    protected $versionStringPrinted;

    static function main($argv = [])
    {
        $options = getopt("f:t:i:c:j:s:hvu");
        if (!isset($argv[1])) {
            file_put_contents('php://stderr', 'Not enough arguments' . PHP_EOL);
            self::help();
            exit(1);
        }
        $sorting = -1;
        if (isset($options['s'])) {
            $value = strtoupper($options['s']);
            if (isset(UI::${'PHPD_SORT_OPT_' . $value})) {
                $sorting = UI::${'PHPD_SORT_OPT_' . $value};
            }
        }

        if (boolval(preg_match('/^\-/', $argv[1]))) {
            if (isset($options['h'])) {
                self::help();
                exit(0);
            }

            if (isset($options['v'])) {
                self::version();
                exit(0);
            }

            if (isset($options['u'])) {
                self::handleSelfUpdate();
                exit(0);
            } elseif (isset($options['f'])) {
                $file = $options['f'];
            } else {
                file_put_contents('php://stderr', 'No file to parse' . PHP_EOL);
                exit(1);
            }
        } else {
            $file = $argv[1];
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

    static function help()
    {
        echo 'This is a parser for API Blueprint files in PHP.' . PHP_EOL . PHP_EOL;
        echo "The following options can be used:.\n";
        echo "\t-f\tSpecifies the file to parse.\n";
        echo "\t-t\tSpecifies the template to use. (defaults to 'default')\n";
        echo "\t-s\tSort displayed values [All|None|Structures|Webservices] (defaults to the way the objects are in the file)\n";
        echo "\t-i\tSpecifies an image to display in the header.\n";
        echo "\t-c\tSpecifies a CSS file to include (value is put in a link element without checking).\n";
        echo "\t-c\tSpecifies a JS file to include (value is put in a script element without checking).\n";
        echo "\t-h\tDisplays this text.\n";
    }

    static function version()
    {
        $version = self::release_id();
        echo 'PHPDraft: ' . $version;
    }

    /**
     * Get the version number
     *
     * @return string
     */
    static function release_id()
    {
        return (VERSION === '0') ? @exec('git describe --tags 2>&1') : VERSION;
    }

    /**
     * @since    Method available since Release 1.4
     */
    protected function handleSelfUpdate()
    {
        self::printVersionString();
        $localFilename = realpath($_SERVER['argv'][0]);
        if (!is_writable($localFilename)) {
            print 'No write permission to update ' . $localFilename . "\n";
            exit(3);
        }
        if (!extension_loaded('openssl')) {
            print "The OpenSSL extension is not loaded.\n";
            exit(3);
        }
        //set POST variables
        //https://github.com/SMillerDev/phpdraft/releases/download/1.3.2/phpdraft.phar
        $url = 'https://github.com/SMillerDev/phpdraft/releases/latest';
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result  = curl_exec($ch);
        $matches = [];
        preg_match("/href=\"https:\/\/github.com\/SMillerDev\/phpdraft\/releases\/tag\/([0-9.]*)\"/", $result,
            $matches);
        curl_close($ch);
        $remoteFilename = sprintf(
            'https://github.com/SMillerDev/phpdraft/releases/download/%s/phpdraft.phar',
            $matches[0]
        );

        $tempFilename = tempnam(sys_get_temp_dir(), 'phpdraft') . '.phar';
        // Workaround for https://bugs.php.net/bug.php?id=65538
        $caFile = dirname($tempFilename) . '/ca.pem';
        copy(__PHPDRAFT_PHAR_ROOT__ . '/ca.pem', $caFile);
        print 'Updating the PHPDraft PHAR ... ';
        $options = [
            'ssl' => [
                'allow_self_signed' => false,
                'cafile'            => $caFile,
                'verify_peer'       => true,
            ],
        ];
        file_put_contents(
            $tempFilename,
            file_get_contents(
                $remoteFilename,
                false,
                stream_context_create($options)
            )
        );
        chmod($tempFilename, 0777 & ~umask());
        try {
            $phar = new Phar($tempFilename);
            unset($phar);
            rename($tempFilename, $localFilename);
            unlink($caFile);
        } catch (Throwable $_e) {
            $e = $_e;
        } catch (Exception $_e) {
            $e = $_e;
        }
        if (isset($e)) {
            unlink($caFile);
            unlink($tempFilename);
            print " done\n\n" . $e->getMessage() . "\n";
            exit(2);
        }
        print " done\n";
        exit(0);
    }

    private function printVersionString()
    {
        print self::version() . "\n\n";
    }

    /**
     * @return string
     *
     * @since Method available since Release 4.8.13
     */
    public static function series()
    {
        if (strpos(self::release_id(), '-')) {
            $version = explode('-', self::release_id())[0];
        } else {
            $version = self::release_id();
        }

        return implode('.', array_slice(explode('.', $version), 0, 2));
    }

    /**
     * @return string
     *
     * @since Method available since Release 4.0.0
     */
    public static function getReleaseChannel()
    {
        if (strpos(self::release_id(), '-') !== false) {
            return '-nightly';
        }

        return '';
    }

    /**
     * @since Method available since Release 1.4
     */
    protected function handleVersionCheck()
    {
        $this->printVersionString();
        $latestVersion = file_get_contents('https://phar.phpdraft.de/latest-version-of/phpdraft');
        $isOutdated    = version_compare($latestVersion, self::release_id(), '>');
        if ($isOutdated) {
            print "You are not using the latest version of PHPDraft.\n";
            print 'Use "phpdraft --self-upgrade" to install PHPDraft ' . $latestVersion . "\n";
        } else {
            print "You are using the latest version of PHPDraft.\n";
        }
        exit(0);
    }
}