<?php

/**
 * This file contains the HierarchyElementChildTestBase.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Model\HierarchyElement;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class HierarchyElementChildTest
 * @package PHPDraft\Model\Tests
 */
abstract class HierarchyElementChildTestBase extends LunrBaseTest
{
    /**
     * Mock of the parent class
     *
     * @var HierarchyElement|MockObject
     */
    protected $parent;

    public function setUp(): void
    {
        $this->parent = $this->getMockBuilder('\PHPDraft\Model\HierarchyElement')
                             ->getMock();
    }
}
