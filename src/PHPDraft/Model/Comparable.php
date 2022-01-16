<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: smillernl
 * Date: 28-10-16
 * Time: 17:23.
 */

namespace PHPDraft\Model;

interface Comparable
{
    /**
     * Check if item is the same as other item.
     *
     * @param object $b Object to compare to
     *
     * @return bool
     */
    public function is_equal_to(object $b): bool;
}
