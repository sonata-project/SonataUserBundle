<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Tests\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Menu\ProfileMenuBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
final class ProfileMenuBuilderTest extends TestCase
{
    public function testCreateProfileMenu(): void
    {
        $menu = $this->createMock(ItemInterface::class);
        $factory = $this->createMock(FactoryInterface::class);

        $factory->expects($this->once())
            ->method('createItem')
            ->willReturn($menu);

        $translator = $this->createMock(TranslatorInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $builder = new ProfileMenuBuilder($factory, $translator, [], $eventDispatcher);

        $genMenu = $builder->createProfileMenu();

        $this->assertInstanceOf(ItemInterface::class, $genMenu);
    }
}
