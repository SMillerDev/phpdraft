<?php

declare(strict_types=1);

/**
 * This file contains the Version.php.
 *
 * @package PHPDraft\Out
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out;

/**
 * Class Version.
 */
class Version
{
    /**
     * Return the version.
     *
     * @return void
     */
    public static function version(): void
    {
        $version = self::release_id();
        echo 'PHPDraft: ' . $version;
    }

    /**
     * Get the version number.
     *
     * @return string
     */
    public static function release_id(): string
    {
        return (VERSION === '0') ? @exec('git describe --tags 2>&1') : VERSION;
    }

    /**
     * Print the series of the update.
     *
     * @return string Series
     */
    public function series(): string
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
    public function getReleaseChannel(): string
    {
        if (str_contains(self::release_id(), '-')) {
            return '-nightly';
        }

        return '';
    }
}
