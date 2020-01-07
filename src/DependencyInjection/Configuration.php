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

namespace Sonata\UserBundle\DependencyInjection;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\UserBundle\Admin\Entity\GroupAdmin;
use Sonata\UserBundle\Admin\Entity\UserAdmin;
use Sonata\UserBundle\Entity\BaseGroup;
use Sonata\UserBundle\Entity\BaseUser;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('sonata_user');

        // Keep compatibility with symfony/config < 4.2
        if (!method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->root('sonata_user');
        } else {
            $rootNode = $treeBuilder->getRootNode();
        }

        $supportedManagerTypes = ['orm', 'mongodb'];

        $rootNode
            ->children()
                ->booleanNode('security_acl')->defaultFalse()->end()
                ->arrayNode('table')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('user_group')->defaultValue('fos_user_user_group')->end()
                    ->end()
                ->end()
                ->scalarNode('impersonating_route')->end()
                ->arrayNode('impersonating')
                    ->children()
                        ->scalarNode('route')->defaultFalse()->end()
                        ->arrayNode('parameters')
                            ->useAttributeAsKey('id')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('manager_type')
                    ->defaultValue('orm')
                    ->validate()
                        ->ifNotInArray($supportedManagerTypes)
                        ->thenInvalid('The manager type %s is not supported. Please choose one of '.json_encode($supportedManagerTypes))
                    ->end()
                ->end()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('group')->cannotBeEmpty()->defaultValue(BaseGroup::class)->end()
                        ->scalarNode('user')->cannotBeEmpty()->defaultValue(BaseUser::class)->end()
                    ->end()
                ->end()

                ->scalarNode('mailer')->defaultValue('sonata.user.mailer.default')->info('Custom mailer used to send reset password emails')->end()
            ->end()
        ;

        $this->addAdminSection($rootNode);
        $this->addGoogleAuthenticatorSection($rootNode);
        $this->addProfileSection($rootNode);

        return $treeBuilder;
    }

    private function addAdminSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('group')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue(GroupAdmin::class)->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue(CRUDController::class)->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataUserBundle')->end()
                            ->end()
                        ->end()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue(UserAdmin::class)->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue(CRUDController::class)->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataUserBundle')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ;
    }

    private function addGoogleAuthenticatorSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('google_authenticator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('server')->cannotBeEmpty()->end()
                        ->scalarNode('enabled')->defaultFalse()->end()
                        ->arrayNode('ip_white_list')
                            ->prototype('scalar')->end()
                            ->defaultValue(['127.0.0.1'])
                            ->info('IPs for which 2FA will be skipped.')
                        ->end()
                        ->arrayNode('forced_for_role')
                            ->prototype('scalar')->end()
                            ->defaultValue(['ROLE_ADMIN'])
                            ->info('User roles for which 2FA is mandatory.')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addProfileSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('profile')
                    ->addDefaultsIfNotSet()
                    ->fixXmlConfig('block')
                    ->children()
                        ->scalarNode('default_avatar')->defaultValue('bundles/sonatauser/default_avatar.png')->end()
                        ->scalarNode('template')
                            ->info('This is the profile template. You should extend your profile actions template by using {% extends sonata_user.profileTemplate %}.')
                            ->cannotBeEmpty()
                            ->defaultValue('@SonataUser/Profile/action.html.twig')
                        ->end()
                        ->scalarNode('menu_builder')
                            ->info('MenuBuilder::createProfileMenu(array $itemOptions = []): ItemInterface is used to build profile menu.')
                            ->defaultValue('sonata.user.profile.menu_builder.default')
                            ->cannotBeEmpty()
                        ->end()
                        ->arrayNode('blocks')
                            ->info('Define your user profile block here.')
                            ->defaultValue($this->getProfileBlocksDefaultValues())
                            ->prototype('array')
                                ->fixXmlConfig('setting')
                                ->children()
                                    ->scalarNode('type')->cannotBeEmpty()->end()
                                    ->arrayNode('settings')
                                    ->useAttributeAsKey('id')
                                    ->prototype('variable')->defaultValue([])->end()
                                    ->end()
                                    ->scalarNode('position')->defaultValue('right')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('menu')
                            ->info('Define your user profile menu records here.')
                            ->defaultValue($this->getProfileMenuDefaultValues())
                            ->prototype('array')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('route')->cannotBeEmpty()->end()
                                ->arrayNode('route_parameters')
                                ->defaultValue([])
                                ->prototype('array')->end()
                                ->end()
                                ->scalarNode('label')->cannotBeEmpty()->end()
                                ->scalarNode('domain')->defaultValue('messages')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Returns default values for profile menu (to avoid BC Break).
     */
    private function getProfileMenuDefaultValues(): array
    {
        return [
            [
                'route' => 'sonata_user_profile_dashboard',
                'label' => 'link_show_profile',
                'domain' => 'SonataUserBundle',
                'route_parameters' => [],
            ],
        ];
    }

    private function getProfileBlocksDefaultValues(): array
    {
        return [
            [
                'position' => 'left',
                'type' => 'sonata.user.block.account',
                'settings' => [
                    'template' => '@SonataUser/Block/account_dashboard.html.twig',
                    ],
            ],
            [
                'position' => 'right',
                'type' => 'sonata.block.service.text',
                'settings' => ['content' => "<h2>Welcome!</h2> <p>This is a sample user profile dashboard, feel free to override it in the configuration! Want to make this text dynamic? For instance display the user's name? Create a dedicated block and edit the configuration!</p>"],
            ],
        ];
    }
}
