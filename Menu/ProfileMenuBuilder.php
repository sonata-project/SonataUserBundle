<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\UserBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ProfileMenuBuilder
 *
 * @package Sonata\UserBundle\Menu
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class ProfileMenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var array
     */
    private $routes;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param FactoryInterface         $factory
     * @param TranslatorInterface      $translator
     * @param array                    $routes     Routes to add to the menu (format: array(array('label' => ..., 'route' => ...)))
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(FactoryInterface $factory, TranslatorInterface $translator, array $routes, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory         = $factory;
        $this->translator      = $translator;
        $this->routes          = $routes;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createProfileMenu(ItemInterface $menu = null)
    {
        if (null === $menu) {
            $menu = $this->factory->createItem('profile', array('childrenAttributes' => array('class' => 'nav nav-list')));
        }

        foreach ($this->routes as $route) {
            $menu->addChild(
                $this->translator->trans($route['label'], array(), $route['domain']),
                array('route' => $route['route'], 'routeParameters' => $route['route_parameters'])
            );
        }

        $event = new ProfileMenuEvent($menu);
        $this->eventDispatcher->dispatch('sonata.user.profile.configure_menu', $event);

        return $menu;
    }
}