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

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
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
class ProfileMenuBlockService extends BaseBlockService
{
    /**
     * @var ProfileMenuBuilder
     */
    private $menuBuilder;

    /**
     * Constructor
     *
     * @param string             $name
     * @param EngineInterface    $templating
     * @param ProfileMenuBuilder $menuBuilder
     */
    public function __construct($name, EngineInterface $templating, ProfileMenuBuilder $menuBuilder)
    {
        parent::__construct($name, $templating);

        $this->menuBuilder = $menuBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        if ("" === ($menu = $blockContext->getSetting('menu_name')) || null === $menu) {
            $menu = $this->menuBuilder->createProfileMenu(array('childrenAttributes' => array('class' => $blockContext->getSetting('menu_class'))));
            $menu->setCurrentUri($blockContext->getSetting('current_uri'));
        }

        return $this->renderPrivateResponse($blockContext->getTemplate(), array(
            'menu'         => $menu,
            'menu_options' => $this->getMenuOptions($blockContext->getSettings()),
            'block'        => $blockContext->getBlock(),
            'context'      => $blockContext
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        $form->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('title', 'text', array('required' => false)),
                array('menu_name', 'string', array('required' => false)),
                array('menu_class'), 'string', array('required' => false),
                array('current_class', 'string', array('required' => false)),
                array('first_class', 'string', array('required' => false)),
                array('last_class', 'string', array('required' => false)),
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title'         => 'User Profile Menu',
            'template'      => 'SonataUserBundle:Block:profile_menu.html.twig',
            'menu_name'     => "",
            'menu_class'    => "nav nav-list",
            'current_class' => 'active',
            'first_class'   => false,
            'last_class'    => false,
            'current_uri'   => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'User Profile Menu';
    }

    /**
     * Replaces setting keys with knp menu item options keys
     *
     * @param array $settings
     */
    protected function getMenuOptions(array $settings)
    {
        $mapping = array(
            'current_class' => 'currentClass',
            'first_class'   => 'firstClass',
            'last_class'    => 'lastClass'
        );

        $options = array();

        foreach ($settings as $key => $value) {
            if (array_key_exists($key, $mapping)) {
                $options[$mapping[$key]] = $value;
            }
        }

        return $options;
    }
}