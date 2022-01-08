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
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @final since sonata-project/user-bundle 4.15
 *
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
    public function fixImpersonating(array $config): array
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
    public function configureClass(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.user.class', $config['class']['user']);
    }

    /**
     * @param array<string, mixed> $config
     */
    public function configureAdminClass(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.admin.user.class', $config['admin']['user']['class']);
    }

    /**
     * @param array<string, mixed> $config
     */
    public function configureTranslationDomain(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.admin.user.translation_domain', $config['admin']['user']['translation']);
    }

    /**
     * @param array<string, mixed> $config
     */
    public function configureController(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.user.admin.user.controller', $config['admin']['user']['controller']);
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
}
