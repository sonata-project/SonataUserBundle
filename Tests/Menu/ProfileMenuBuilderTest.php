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

use Sonata\UserBundle\Menu\ProfileMenuBuilder;
use Sonata\UserBundle\Tests\Helpers\PHPUnit_Framework_TestCase;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class ProfileMenuBuilderTest extends PHPUnit_Framework_TestCase
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

        $builder = new ProfileMenuBuilder($factory, $translator, array(), $eventDispatcher);

        $genMenu = $builder->createProfileMenu();

        $this->assertInstanceOf('Knp\Menu\ItemInterface', $genMenu);
    }
}
