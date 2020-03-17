<?php
declare(strict_types=1);

/**
 * This file contains the Sorting.php.
 *
 * @package PHPDraft\Out
 *
 * @author  Sean Molenaar <sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out;

/**
 * Sorting constants.
 */
class Sorting
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
     * Sets sorting to no data structures.
     *
     * @var int
     */
    public static $PHPD_SORT_NONE = -1;

    /**
     * Check if structures should be sorted.
     *
     * @param int $sort The sorting level.
     *
     * @return bool
     */
    public static function sortStructures(int $sort): bool
    {
        return $sort === self::$PHPD_SORT_ALL || $sort === self::$PHPD_SORT_STRUCTURES;
    }

    /**
     * Check if services should be sorted.
     *
     * @param int $sort The sorting level.
     *
     * @return bool
     */
    public static function sortServices(int $sort): bool
    {
        return $sort === self::$PHPD_SORT_ALL || $sort === self::$PHPD_SORT_WEBSERVICES;
    }
}
