<?php

namespace PHPDraft\Parse\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Parse\BaseHtmlGenerator;
use PHPDraft\Parse\HtmlGenerator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Class BaseHtmlGeneratorTest
 * @covers  \PHPDraft\Parse\BaseHtmlGenerator
 */
class BaseHtmlGeneratorTest extends LunrBaseTest
{
    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->class      = $this->getMockForAbstractClass('PHPDraft\Parse\BaseHtmlGenerator');
        $this->reflection = new ReflectionClass('PHPDraft\Parse\BaseHtmlGenerator');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * * @covers  \PHPDraft\Parse\BaseHtmlGenerator::init
     */
    public function testInit(): void
    {
        $data = json_decode(file_get_contents(TEST_STATICS . '/drafter/json/index.json'));
        $init = $this->class->init($data);

        $this->assertPropertySame('object', $data);
    }
}
