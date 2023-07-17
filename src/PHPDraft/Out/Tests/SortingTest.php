<?php

/**
 * This file contains the SortingTest.php
 *
 * @package PHPDraft\Out
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Out\Sorting;
use PHPDraft\Out\Version;
use ReflectionClass;

/**
 * Class SortingTest
 *
 * @covers \PHPDraft\Out\Sorting
 */
class SortingTest extends LunrBaseTest
{
    /**
     * Test if service sorting is determined correctly.
     *
     * @covers \PHPDraft\Out\Sorting::sortServices
     */
    public function testSortsServicesIfNeeded(): void
    {
        $this->assertTrue(Sorting::sortServices(Sorting::PHPD_SORT_ALL));
        $this->assertTrue(Sorting::sortServices(Sorting::PHPD_SORT_WEBSERVICES));
        $this->assertFalse(Sorting::sortServices(Sorting::PHPD_SORT_NONE));
        $this->assertFalse(Sorting::sortServices(Sorting::PHPD_SORT_STRUCTURES));
    }

    /**
     * Test if structure sorting is determined correctly.
     *
     * @covers \PHPDraft\Out\Sorting::sortStructures
     */
    public function testSortsStructureIfNeeded(): void
    {
        $this->assertTrue(Sorting::sortStructures(Sorting::PHPD_SORT_ALL));
        $this->assertTrue(Sorting::sortStructures(Sorting::PHPD_SORT_STRUCTURES));
        $this->assertFalse(Sorting::sortStructures(Sorting::PHPD_SORT_NONE));
        $this->assertFalse(Sorting::sortStructures(Sorting::PHPD_SORT_WEBSERVICES));
    }
}
