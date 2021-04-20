<?php

namespace PHPDraft\Out\Tests;

use Lunr\Halo\LunrBaseTest;
use PHPDraft\Out\TwigFactory;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TwigFactoryTest extends LunrBaseTest
{
    /**
     * Check factory
     *
     * @covers \PHPDraft\Out\TwigFactory::get
     * @group twig
     */
    public function testFactory(): void
    {
        $loader = new ArrayLoader();

        $this->assertInstanceOf(Environment::class, TwigFactory::get($loader));
    }
}
