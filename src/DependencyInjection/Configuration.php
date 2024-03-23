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
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UndefinedInterfaceMethod
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
                ->arrayNode('impersonating')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('route')->isRequired()->cannotBeEmpty()->end()
                        ->arrayNode('parameters')
                            ->defaultValue([])
                            ->useAttributeAsKey('id')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('manager_type')
                    ->cannotBeEmpty()
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
                        ->scalarNode('default_avatar')->cannotBeEmpty()->defaultValue('bundles/sonatauser/default_avatar.png')->end()
                    ->end()
                ->end()
                ->scalarNode('mailer')->cannotBeEmpty()->defaultValue('sonata.user.mailer.default')->info('Custom mailer used to send reset password emails')->end()
            ->end();

        $this->addResettingSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     *
     * @see https://github.com/psalm/psalm-plugin-symfony/issues/174
     */
    private function addResettingSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resetting')
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('retry_ttl')->defaultValue(7200)->end()
                        ->integerNode('token_ttl')->defaultValue(86400)->end()
                        ->arrayNode('email')
                            ->isRequired()
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
