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
        $this->assertTrue(Sorting::sortServices(3));
        $this->assertTrue(Sorting::sortServices(2));
        $this->assertFalse(Sorting::sortServices(-1));
        $this->assertFalse(Sorting::sortServices(1));
        $this->assertFalse(Sorting::sortServices(0));
    }

    /**
     * Test if structure sorting is determined correctly.
     *
     * @covers \PHPDraft\Out\Sorting::sortStructures
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
