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

namespace Sonata\UserBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
final class ProfileMenuBuilder
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
     * @param array $routes Routes to add to the menu (format: [['label' => ..., 'route' => ...]])
     */
    public function __construct(
        FactoryInterface $factory,
        TranslatorInterface $translator,
        array $routes,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->factory = $factory;
        $this->translator = $translator;
        $this->routes = $routes;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array $itemOptions The options given to the created menuItem
     */
    public function createProfileMenu(array $itemOptions = []): ItemInterface
    {
        $menu = $this->factory->createItem('user_profile', $itemOptions);

        $this->buildProfileMenu($menu, $itemOptions);

        return $menu;
    }

    /**
     * @param ItemInterface $menu The item to fill with $routes
     */
    public function buildProfileMenu(ItemInterface $menu, array $itemOptions = []): void
    {
        foreach ($this->routes as $route) {
            $menu->addChild(
                $this->translator->trans($route['label'], [], $route['domain']),
                array_merge($itemOptions, ['route' => $route['route'], 'routeParameters' => $route['route_parameters']])
            );
        }

        $event = new ProfileMenuEvent($menu);
        $this->eventDispatcher->dispatch('sonata.user.profile.configure_menu', $event);
    }
}
