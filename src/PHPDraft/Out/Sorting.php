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
enum Sorting: int
{
    /**
     * Sets sorting to all parts.
     */
    case PHPD_SORT_ALL = 3;

    /**
     * Sets sorting to all webservices.
     */
    case PHPD_SORT_WEBSERVICES = 2;

    /**
     * Sets sorting to all data structures.
     */
    case PHPD_SORT_STRUCTURES = 1;

    /**
     * Sets sorting to no data structures.
     */
    case PHPD_SORT_NONE = -1;

    /**
     * Check if structures should be sorted.
     *
     * @param int $sort The sorting level.
     *
     * @return bool
     */
    public static function sortStructures(int $sort): bool
    {
        return $sort === Sorting::PHPD_SORT_ALL->value || $sort === Sorting::PHPD_SORT_STRUCTURES->value;
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
        return $sort === Sorting::PHPD_SORT_ALL->value || $sort === Sorting::PHPD_SORT_WEBSERVICES->value;
    }
}
