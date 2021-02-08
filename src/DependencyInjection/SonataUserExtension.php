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

use Nelmio\ApiDocBundle\Annotation\Operation;
use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector as DeprecatedDoctrineCollector;
use Sonata\UserBundle\Document\BaseGroup as DocumentGroup;
use Sonata\UserBundle\Document\BaseUser as DocumentUser;
use Sonata\UserBundle\Entity\BaseGroup as EntityGroup;
use Sonata\UserBundle\Entity\BaseUser as EntityUser;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataUserExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('twig')) {
            // add custom form widgets
            $container->prependExtensionConfig('twig', ['form_themes' => ['@SonataUser/Form/form_admin_fields.html.twig']]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $config = $this->fixImpersonating($config);

        $bundles = $container->getParameter('kernel.bundles');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.xml');
            $loader->load(sprintf('admin_%s.xml', $config['manager_type']));
        }

        $loader->load(sprintf('%s.xml', $config['manager_type']));

        $this->aliasManagers($container, $config['manager_type']);

        $loader->load('form.xml');

        if (class_exists('Google\Authenticator\GoogleAuthenticator')) {
            @trigger_error(
                'The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.',
                \E_USER_DEPRECATED
            );
        }

        if (class_exists('Google\Authenticator\GoogleAuthenticator') ||
            class_exists('Sonata\GoogleAuthenticator\GoogleAuthenticator')) {
            $loader->load('google_authenticator.xml');
        }

        $loader->load('twig.xml');
        $loader->load('command.xml');
        $loader->load('actions.xml');
        $loader->load('mailer.xml');

        if ('orm' === $config['manager_type'] && isset(
            $bundles['FOSRestBundle'],
            $bundles['NelmioApiDocBundle'],
            $bundles['JMSSerializerBundle']
        )) {
            $loader->load('serializer.xml');

            $loader->load('api_form.xml');
            if (class_exists(Operation::class)) {
                $loader->load('api_controllers.xml');
            } else {
                $loader->load('api_controllers_legacy.xml');
            }
        }

        if ($config['security_acl']) {
            $loader->load('security_acl.xml');
        }

        $this->checkManagerTypeToModelTypesMapping($config);

        if (isset($bundles['SonataDoctrineBundle'])) {
            $this->registerSonataDoctrineMapping($config);
        } else {
            // NEXT MAJOR: Remove next line and throw error when not registering SonataDoctrineBundle
            $this->registerDoctrineMapping($config);
        }

        $this->configureAdminClass($config, $container);
        $this->configureClass($config, $container);

        $this->configureTranslationDomain($config, $container);
        $this->configureController($config, $container);
        $this->configureMailer($config, $container);

        $container->setParameter('sonata.user.default_avatar', $config['profile']['default_avatar']);

        $container->setParameter('sonata.user.impersonating', $config['impersonating']);

        $this->configureGoogleAuthenticator($config, $container);

        $this->createDoctrineCommonBackwardCompatibilityAliases();
    }

    /**
     * @throws \RuntimeException
     *
     * @return array
     */
    public function fixImpersonating(array $config)
    {
        if (isset($config['impersonating'], $config['impersonating_route'])) {
            throw new \RuntimeException('you can\'t have `impersonating` and `impersonating_route` keys defined at the same time');
        }

        if (isset($config['impersonating_route'])) {
            $config['impersonating'] = [
                'route' => $config['impersonating_route'],
                'parameters' => [],
            ];
        }

        if (!isset($config['impersonating']['parameters'])) {
            $config['impersonating']['parameters'] = [];
        }

        if (!isset($config['impersonating']['route'])) {
            $config['impersonating'] = false;
        }

        return $config;
    }

    /**
     * @param array $config
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function configureGoogleAuthenticator($config, ContainerBuilder $container)
    {
        $container->setParameter('sonata.user.google.authenticator.enabled', $config['google_authenticator']['enabled']);

        if (!$config['google_authenticator']['enabled']) {
            $container->removeDefinition('sonata.user.google.authenticator');
            $container->removeDefinition('sonata.user.google.authenticator.provider');
            $container->removeDefinition('sonata.user.google.authenticator.interactive_login_listener');
            $container->removeDefinition('sonata.user.google.authenticator.request_listener');

            return;
        }

        if (!class_exists('Google\Authenticator\GoogleAuthenticator')
            && !class_exists('Sonata\GoogleAuthenticator\GoogleAuthenticator')) {
            throw new \RuntimeException('Please add "sonata-project/google-authenticator" package');
        }

        $container->setParameter('sonata.user.google.authenticator.forced_for_role', $config['google_authenticator']['forced_for_role']);

        // NEXT_MAJOR: Remove this checks and only set the `trusted_ip_list`.
        if (\count($config['google_authenticator']['ip_white_list']) > 0 && $config['google_authenticator']['trusted_ip_list'] !== ['127.0.0.1']) {
            throw new \LogicException('Please use only "trusted_ip_list" parameter, "ip_white_list" is deprecated.');
        }
        $trustedIpList = $config['google_authenticator']['trusted_ip_list'];
        if (\count($config['google_authenticator']['ip_white_list']) > 0) {
            $trustedIpList = $config['google_authenticator']['ip_white_list'];
        }
        // NEXT_MAJOR: Remove `sonata.user.google.authenticator.ip_white_list` parameter.
        $container->setParameter('sonata.user.google.authenticator.ip_white_list', $trustedIpList);
        $container->setParameter('sonata.user.google.authenticator.trusted_ip_list', $trustedIpList);

        $container->getDefinition('sonata.user.google.authenticator.provider')
            ->replaceArgument(0, $config['google_authenticator']['server']);
    }

    /**
     * @param array $config
     */
    public function configureClass($config, ContainerBuilder $container): void
    {
        if ('orm' === $config['manager_type']) {
            $modelType = 'entity';
        } elseif ('mongodb' === $config['manager_type']) {
            $modelType = 'document';
        } else {
            throw new \InvalidArgumentException(sprintf('Invalid manager type "%s".', $config['manager_type']));
        }

        $container->setParameter(sprintf('sonata.user.admin.user.%s', $modelType), $config['class']['user']);
        $container->setParameter(sprintf('sonata.user.admin.group.%s', $modelType), $config['class']['group']);
    }

    /**
     * @param array $config
     */
    public function configureAdminClass($config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.admin.user.class', $config['admin']['user']['class']);
        $container->setParameter('sonata.user.admin.group.class', $config['admin']['group']['class']);
    }

    /**
     * @param array $config
     */
    public function configureTranslationDomain($config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.admin.user.translation_domain', $config['admin']['user']['translation']);
        $container->setParameter('sonata.user.admin.group.translation_domain', $config['admin']['group']['translation']);
    }

    /**
     * @param array $config
     */
    public function configureController($config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.admin.user.controller', $config['admin']['user']['controller']);
        $container->setParameter('sonata.user.admin.group.controller', $config['admin']['group']['controller']);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     */
    public function registerDoctrineMapping(array $config): void
    {
        @trigger_error(
            'Using this method is deprecated since sonata-project/user-bundle 4.7. You should instead register SonataDoctrineBundle and use `registerSonataDoctrineMapping()`',
            \E_USER_DEPRECATED
        );

        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector = DeprecatedDoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['user'], 'mapManyToMany', [
            'fieldName' => 'groups',
            'targetEntity' => $config['class']['group'],
            'cascade' => [],
            'joinTable' => [
                'name' => $config['table']['user_group'],
                'joinColumns' => [
                    [
                        'name' => 'user_id',
                        'referencedColumnName' => 'id',
                        'onDelete' => 'CASCADE',
                    ],
                ],
                'inverseJoinColumns' => [[
                    'name' => 'group_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ]],
            ],
        ]);
    }

    /**
     * Adds aliases for user & group managers depending on $managerType.
     *
     * @param string $managerType
     */
    protected function aliasManagers(ContainerBuilder $container, $managerType): void
    {
        $container
            ->setAlias('sonata.user.user_manager', sprintf('sonata.user.%s.user_manager', $managerType))
            ->setPublic(true);
        $container
            ->setAlias('sonata.user.group_manager', sprintf('sonata.user.%s.group_manager', $managerType))
            ->setPublic(true);
    }

    private function checkManagerTypeToModelTypesMapping(array $config): void
    {
        $managerType = $config['manager_type'];

        if (!\in_array($managerType, ['orm', 'mongodb'], true)) {
            throw new \InvalidArgumentException(sprintf('Invalid manager type "%s".', $managerType));
        }

        $this->prohibitModelTypeMapping(
            $config['class']['user'],
            'orm' === $managerType ? DocumentUser::class : EntityUser::class,
            $managerType
        );

        $this->prohibitModelTypeMapping(
            $config['class']['group'],
            'orm' === $managerType ? DocumentGroup::class : EntityGroup::class,
            $managerType
        );
    }

    /**
     * Prohibit using wrong model type mapping.
     */
    private function prohibitModelTypeMapping(
        string $actualModelClass,
        string $prohibitedModelClass,
        string $managerType
    ): void {
        if (is_a($actualModelClass, $prohibitedModelClass, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Model class "%s" does not correspond to manager type "%s".',
                    $actualModelClass,
                    $managerType
                )
            );
        }
    }

    private function configureMailer(array $config, ContainerBuilder $container): void
    {
        $container->setAlias('sonata.user.mailer', $config['mailer']);
    }

    private function registerSonataDoctrineMapping(array $config): void
    {
        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation(
            $config['class']['user'],
            'mapManyToMany',
            OptionsBuilder::createManyToMany('groups', $config['class']['group'])
                ->addJoinTable($config['table']['user_group'], [[
                    'name' => 'user_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ]], [[
                    'name' => 'group_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ]])
        );
    }

    /**
     * We MUST remove this method when support for "friendsofsymfony/user-bundle" is dropped
     * or adapted to work with "doctrine/common:^3".
     */
    private function createDoctrineCommonBackwardCompatibilityAliases(): void
    {
        if (!interface_exists(\Doctrine\Common\Persistence\ObjectManager::class)) {
            class_alias(\Doctrine\Persistence\ObjectManager::class, \Doctrine\Common\Persistence\ObjectManager::class);
        }

        if (!class_exists(\Doctrine\Common\Persistence\Event\LifecycleEventArgs::class)) {
            class_alias(\Doctrine\Persistence\Event\LifecycleEventArgs::class, \Doctrine\Common\Persistence\Event\LifecycleEventArgs::class);
        }
    }
}
