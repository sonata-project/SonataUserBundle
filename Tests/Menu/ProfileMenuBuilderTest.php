<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Tests\Menu;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Menu\ProfileMenuBuilder;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class ProfileMenuBuilderTest extends TestCase
{
    public function testCreateProfileMenu()
    {
        $menu = $this->createMock('Knp\Menu\ItemInterface');
        $factory = $this->createMock('Knp\Menu\FactoryInterface');

        $factory->expects($this->once())
            ->method('createItem')
            ->will($this->returnValue($menu));

        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $builder = new ProfileMenuBuilder($factory, $translator, [], $eventDispatcher);

        $genMenu = $builder->createProfileMenu();

        $this->assertInstanceOf('Knp\Menu\ItemInterface', $genMenu);
    }
}
