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
enum Sorting
{
    /**
     * Sets sorting to all parts.
     */
    case PHPD_SORT_ALL;

    /**
     * Sets sorting to all webservices.
     */
    case PHPD_SORT_WEBSERVICES;

    /**
     * Sets sorting to all data structures.
     */
    case PHPD_SORT_STRUCTURES;

    /**
     * Sets sorting to no data structures.
     */
    case PHPD_SORT_NONE;

    /**
     * Check if structures should be sorted.
     *
     * @param Sorting $sort The sorting level.
     *
     * @return bool
     */
    public static function sortStructures(Sorting $sort): bool
    {
        return $sort === self::PHPD_SORT_ALL || $sort === self::PHPD_SORT_STRUCTURES;
    }

    /**
     * Check if services should be sorted.
     *
     * @param Sorting $sort The sorting level.
     *
     * @return bool
     */
    public static function sortServices(Sorting $sort): bool
    {
        return $sort === self::PHPD_SORT_ALL || $sort === self::PHPD_SORT_WEBSERVICES;
    }
}
