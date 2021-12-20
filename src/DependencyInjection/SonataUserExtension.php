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

use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector;
use Sonata\UserBundle\Document\BaseGroup as DocumentGroup;
use Sonata\UserBundle\Document\BaseUser as DocumentUser;
use Sonata\UserBundle\Entity\BaseGroup as EntityGroup;
use Sonata\UserBundle\Entity\BaseUser as EntityUser;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataUserExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('twig')) {
            // add custom form widgets
            $container->prependExtensionConfig('twig', ['form_themes' => ['@SonataUser/Form/form_admin_fields.html.twig']]);
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $config = $this->fixImpersonating($config);

        $bundles = $container->getParameter('kernel.bundles');
        \assert(\is_array($bundles));

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.php');
            $loader->load(sprintf('admin_%s.php', $config['manager_type']));
        }

        $loader->load(sprintf('%s.php', $config['manager_type']));

        $loader->load('twig.php');
        $loader->load('actions.php');
        $loader->load('listener.php');
        $loader->load('mailer.php');
        $loader->load('form.php');
        $loader->load('security.php');
        $loader->load('util.php');
        $loader->load('validator.php');

        if (true === $config['security_acl']) {
            $loader->load('security_acl.php');
        }

        $this->checkManagerTypeToModelTypesMapping($config);

        if ('orm' === $config['manager_type']) {
            if (!isset($bundles['SonataDoctrineBundle'])) {
                throw new \RuntimeException('You must register SonataDoctrineBundle to use SonataUserBundle.');
            }

            $this->registerSonataDoctrineMapping($config);
        }

        $this->configureAdminClass($config, $container);
        $this->configureClass($config, $container);

        $this->configureTranslationDomain($config, $container);
        $this->configureController($config, $container);
        $this->configureMailer($config, $container);
        $this->configureResetting($container, $config);

        $container->setParameter('sonata.user.default_avatar', $config['profile']['default_avatar']);
        $container->setParameter('sonata.user.impersonating', $config['impersonating']);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws \RuntimeException
     *
     * @return array<string, mixed>
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
     * @param array<string, mixed> $config
     */
    public function configureClass($config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.user.class', $config['class']['user']);
        $container->setParameter('sonata.user.group.class', $config['class']['group']);
    }

    /**
     * @param array<string, mixed> $config
     */
    public function configureAdminClass($config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.admin.user.class', $config['admin']['user']['class']);
        $container->setParameter('sonata.user.admin.group.class', $config['admin']['group']['class']);
    }

    /**
     * @param array<string, mixed> $config
     */
    public function configureTranslationDomain($config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.admin.user.translation_domain', $config['admin']['user']['translation']);
        $container->setParameter('sonata.user.admin.group.translation_domain', $config['admin']['group']['translation']);
    }

    /**
     * @param array<string, mixed> $config
     */
    public function configureController($config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.admin.user.controller', $config['admin']['user']['controller']);
        $container->setParameter('sonata.user.admin.group.controller', $config['admin']['group']['controller']);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureResetting(ContainerBuilder $container, array $config): void
    {
        $container->setParameter('sonata.user.resetting.retry_ttl', $config['resetting']['retry_ttl']);
        $container->setParameter('sonata.user.resetting.token_ttl', $config['resetting']['token_ttl']);
        $container->setParameter('sonata.user.resetting.email.from_email', [
            $config['resetting']['email']['address'] => $config['resetting']['email']['sender_name'],
        ]);
        $container->setParameter('sonata.user.resetting.email.template', $config['resetting']['email']['template']);
    }

    /**
     * @param array<string, mixed> $config
     */
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

    /**
     * @param array<string, mixed> $config
     */
    private function configureMailer(array $config, ContainerBuilder $container): void
    {
        $container->setAlias('sonata.user.mailer', $config['mailer']);
    }

    /**
     * @param array<string, mixed> $config
     */
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
}
