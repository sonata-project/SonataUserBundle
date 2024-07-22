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

use Sonata\UserBundle\Document\BaseUser as DocumentUser;
use Sonata\UserBundle\Entity\BaseUser as EntityUser;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class SonataUserExtension extends Extension implements PrependExtensionInterface
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

        $bundles = $container->getParameter('kernel.bundles');
        \assert(\is_array($bundles));

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.php');
            $loader->load(sprintf('admin_%s.php', $config['manager_type']));
            $loader->load('actions.php');
        }

        $loader->load(sprintf('%s.php', $config['manager_type']));
        $container->setParameter('sonata.user.manager_type', $config['manager_type']);

        $loader->load('twig.php');
        $loader->load('commands.php');
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

        $resetting = false;

        $this->configureClass($config, $container);
        $this->configureDefaultAvatar($config['profile'], $container);
        if (isset($bundles['SonataAdminBundle'])) {
            $this->configureAdmin($config['admin'], $container);
            // check for reset configuration
            if (isset($config['resetting']['email']['address'], $config['resetting']['email']['sender_name'])) {
                $resetting = true;
                $loader->load('actions_resetting.php');
                $loader->load('mailer.php');
                $this->configureMailer($config, $container);
                // needs to be done after mailer
                $this->configureResetting($config['resetting'], $container);
            }
            $container->getDefinition('sonata.user.action.login')
                ->replaceArgument(8, $resetting);
        }

        $this->configureImpersonation($config['impersonating'], $container);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureClass(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.user.class', $config['class']['user']);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureAdmin(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.admin.user.controller', $config['user']['controller']);

        $container->getDefinition('sonata.user.admin.user')
            ->setClass($config['user']['class'])
            ->addMethodCall('setTranslationDomain', [$config['user']['translation']]);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureResetting(array $config, ContainerBuilder $container): void
    {
        $container->getDefinition('sonata.user.action.request')
            ->replaceArgument(9, $config['retry_ttl']);

        $container->getDefinition('sonata.user.action.check_email')
            ->replaceArgument(4, $config['token_ttl']);

        $container->getDefinition('sonata.user.action.reset')
            ->replaceArgument(8, $config['token_ttl']);

        $container->getDefinition('sonata.user.mailer.default')
            ->replaceArgument(3, [$config['email']['address'] => $config['email']['sender_name']])
            ->replaceArgument(4, $config['email']['template']);
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
    }

    /**
     * Prohibit using wrong model type mapping.
     *
     * @phpstan-param class-string $actualModelClass
     * @phpstan-param class-string $prohibitedModelClass
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
    private function configureDefaultAvatar(array $config, ContainerBuilder $container): void
    {
        $container->getDefinition('sonata.user.twig.global')
            ->replaceArgument(1, $config['default_avatar']);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureImpersonation(array $config, ContainerBuilder $container): void
    {
        $container->getDefinition('sonata.user.twig.global')
            ->replaceArgument(2, $config['enabled'])
            ->replaceArgument(3, $config['route'] ?? '')
            ->replaceArgument(4, $config['parameters'] ?? []);
    }
}
