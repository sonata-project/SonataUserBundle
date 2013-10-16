<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\UserBundle\Tests\Menu;

use Sonata\UserBundle\Menu\ProfileMenuBuilder;


/**
 * Class ProfileMenuBuilderTest
 *
 * @package Sonata\UserBundle\Tests\Menu
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class ProfileMenuBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateProfileMenu()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $factory = $this->getMock('Knp\Menu\FactoryInterface');

        $factory->expects($this->once())
            ->method('createItem')
            ->will($this->returnValue($menu));

        $translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');

        $builder = new ProfileMenuBuilder($factory, $translator, array());

        $genMenu = $builder->createProfileMenu($this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock());

        $this->assertInstanceOf('Knp\Menu\ItemInterface', $genMenu);
    }
}
