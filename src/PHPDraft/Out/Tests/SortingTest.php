<?php

/**
 * This file contains the SortingTest.php
 *
 * @package PHPDraft\Out
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Model\Category;
use PHPDraft\Out\Sorting;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;

/**
 * Class SortingTest
 */
#[CoversClass(Sorting::class)]
class SortingTest extends LunrBaseTestCase
{
    /**
     * Test if service sorting is determined correctly.
     */
    public function testSortsServicesIfNeeded(): void
    {
        $this->assertTrue(Sorting::sortServices(3));
        $this->assertTrue(Sorting::sortServices(2));
        $this->assertFalse(Sorting::sortServices(-1));
        $this->assertFalse(Sorting::sortServices(1));
        $this->assertFalse(Sorting::sortServices(0));
    }

    /**
     * Test if structure sorting is determined correctly.
     */
    public function testSortsStructureIfNeeded(): void
    {
        $this->assertTrue(Sorting::sortStructures(3));
        $this->assertTrue(Sorting::sortStructures(1));
        $this->assertFalse(Sorting::sortStructures(-1));
        $this->assertFalse(Sorting::sortStructures(2));
        $this->assertFalse(Sorting::sortStructures(0));
    }
}
