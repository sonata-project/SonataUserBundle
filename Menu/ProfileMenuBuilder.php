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
     * @param FactoryInterface    $factory
     * @param TranslatorInterface $translator
     * @param array               $routes     Routes to add to the menu (format: array(array('label' => ..., 'route' => ...)))
     */
    public function __construct(FactoryInterface $factory, TranslatorInterface $translator, array $routes)
    {
        $this->factory    = $factory;
        $this->translator = $translator;
        $this->routes     = $routes;
    }

    /**
     * @param Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createProfileMenu(Request $request)
    {
        $menu = $this->factory->createItem('profile', array('childrenAttributes' => array('class' => 'nav nav-list')));

        $menu->setCurrentUri($request->getRequestUri());

        foreach ($this->routes as $route) {
            $label = array_key_exists('domain', $route) ? $this->translator->trans($route['label'], array(), $route['domain']) : $route['label'];
            $menu->addChild($label, array('route' => $route['route']));
        }

        return $menu;
    }
}