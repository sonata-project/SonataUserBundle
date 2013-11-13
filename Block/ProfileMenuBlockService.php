<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\UserBundle\Block;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\MenuBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\UserBundle\Menu\ProfileMenuBuilder;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ProfileMenuBlockService
 *
 * @package Sonata\UserBundle\Block
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class ProfileMenuBlockService extends MenuBlockService
{
    /**
     * @var ProfileMenuBuilder
     */
    private $menuBuilder;

    /**
     * Constructor
     *
     * @param string                $name
     * @param EngineInterface       $templating
     * @param MenuProviderInterface $menuProvider
     * @param ProfileMenuBuilder    $menuBuilder
     */
    public function __construct($name, EngineInterface $templating, MenuProviderInterface $menuProvider, ProfileMenuBuilder $menuBuilder)
    {
        parent::__construct($name, $templating, $menuProvider, array());

        $this->menuBuilder = $menuBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'User Profile Menu';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        parent::setDefaultSettings($resolver);

        $resolver->setDefaults(array(
            'cache_policy' => 'private',
            'menu_class'   => "nav nav-list",
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormSettingsKeys()
    {
        return array_merge(parent::getFormSettingsKeys(), array(
            array('menu_class', 'text', array('required' => false)),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(array $settings)
    {
        $menu = parent::getMenu($settings);

        if (null === $menu || "" === $menu) {
            $menu = $this->menuBuilder->createProfileMenu(array('childrenAttributes' => array('class' => $settings['menu_class'])));
            $menu->setCurrentUri($settings['current_uri']);
        }

        return $menu;
    }
}