<?php

namespace PHPDraft\Out\Tests;

use Lunr\Halo\LunrBaseTestCase;
use PHPDraft\Out\TwigFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

#[CoversClass(TwigFactory::class)]
class TwigFactoryTest extends LunrBaseTestCase
{
    /**
     * Check factory
     */
    #[Group('twig')]
    public function testFactory(): void
    {
        $loader = new ArrayLoader();

        $this->assertInstanceOf(Environment::class, TwigFactory::get($loader));
    }
}
