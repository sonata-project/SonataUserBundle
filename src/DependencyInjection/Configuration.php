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

use Sonata\UserBundle\Admin\Entity\UserAdmin;
use Sonata\UserBundle\Entity\BaseUser;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @final since sonata-project/user-bundle 4.15
 *
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress PossiblyNullReference, PossiblyUndefinedMethod
     *
     * @see https://github.com/psalm/psalm-plugin-symfony/issues/174
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sonata_user');
        $rootNode = $treeBuilder->getRootNode();

        $supportedManagerTypes = ['orm', 'mongodb'];

        $rootNode
            ->children()
                ->booleanNode('security_acl')->defaultFalse()->end()
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
                        ->scalarNode('user')->cannotBeEmpty()->defaultValue(BaseUser::class)->end()
                    ->end()
                ->end()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue(UserAdmin::class)->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('%sonata.admin.configuration.default_controller%')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataUserBundle')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('profile')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_avatar')->defaultValue('bundles/sonatauser/default_avatar.png')->end()
                    ->end()
                ->end()
                ->scalarNode('mailer')->defaultValue('sonata.user.mailer.default')->info('Custom mailer used to send reset password emails')->end()
            ->end();

        $this->addResettingSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @psalm-suppress PossiblyNullReference, PossiblyUndefinedMethod
     *
     * @see https://github.com/psalm/psalm-plugin-symfony/issues/174
     */
    private function addResettingSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resetting')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('retry_ttl')->defaultValue(7200)->end()
                        ->integerNode('token_ttl')->defaultValue(86400)->end()
                        ->arrayNode('email')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('template')->cannotBeEmpty()->defaultValue('@SonataUser/Admin/Security/Resetting/email.html.twig')->end()
                                ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
