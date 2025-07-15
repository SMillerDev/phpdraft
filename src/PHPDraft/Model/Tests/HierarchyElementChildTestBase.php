<?php

/**
 * This file contains the HierarchyElementChildTestBase.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Model\HierarchyElement;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class HierarchyElementChildTest
 */
abstract class HierarchyElementChildTestBase extends LunrBaseTestCase
{
    /**
     * Mock of the parent class
     *
     * @var HierarchyElement|MockObject
     */
    protected HierarchyElement|MockObject $parent;

    public function setUp(): void
    {
        $this->parent = $this->getMockBuilder('\PHPDraft\Model\HierarchyElement')
                             ->getMock();
    }
}
