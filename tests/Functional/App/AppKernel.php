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

namespace Sonata\UserBundle\Tests\Functional\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FOS\RestBundle\FOSRestBundle;
use FOS\UserBundle\FOSUserBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Nelmio\ApiDocBundle\NelmioApiDocBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Sonata\UserBundle\Entity\BaseGroup;
use Sonata\UserBundle\SonataUserBundle;
use Sonata\UserBundle\Tests\Entity\User;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', false);
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new SecurityBundle(),
            new DoctrineBundle(),
            new FOSUserBundle(),
            new FOSRestBundle(),
            new JMSSerializerBundle(),
            new NelmioApiDocBundle(),
            new SonataDoctrineORMAdminBundle(),
            new SonataDoctrineBundle(),
            new KnpMenuBundle(),
            new SonataAdminBundle(),
            new SonataUserBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return $this->getBaseDir().'cache';
    }

    public function getLogDir(): string
    {
        return $this->getBaseDir().'log';
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $routes->import($this->getProjectDir().'/config/routes.yml');
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $containerBuilder->register('templating')->setSynthetic(true);
        $containerBuilder->register('templating.locator')->setSynthetic(true);
        $containerBuilder->register('templating.name_parser')->setSynthetic(true);
        $containerBuilder->register('mailer')->setSynthetic(true);

        $containerBuilder->loadFromExtension('framework', [
            'secret' => '50n474.U53r',
            'session' => [
                'handler_id' => 'session.handler.native_file',
                'storage_id' => 'session.storage.mock_file',
                'name' => 'MOCKSESSID',
            ],
            'translator' => null,
            'validation' => [
                'enabled' => true,
            ],
            'form' => [
                'enabled' => true,
            ],
            'assets' => null,
            'test' => true,
            'profiler' => [
                'enabled' => true,
                'collect' => false,
            ],
        ]);

        $containerBuilder->loadFromExtension('security', [
            'firewalls' => ['api' => ['anonymous' => true]],
            'providers' => ['in_memory' => ['memory' => null]],
        ]);

        $containerBuilder->loadFromExtension('twig', [
            'strict_variables' => '%kernel.debug%',
            'exception_controller' => null,
        ]);

        $containerBuilder->loadFromExtension('doctrine', [
            'dbal' => [
                'connections' => [
                    'default' => [
                        'driver' => 'pdo_sqlite',
                    ],
                ],
            ],
            'orm' => [
                'default_entity_manager' => 'default',
            ],
        ]);

        $containerBuilder->loadFromExtension('fos_user', [
            'user_class' => User::class,
            'group' => [
                'group_class' => BaseGroup::class,
            ],
            'db_driver' => 'orm',
            'firewall_name' => 'api',
            'from_email' => [
                'address' => 'sonatauser@example.com',
                'sender_name' => 'SonataUserBundle',
            ],
            'service' => [
                'mailer' => 'fos_user.mailer.noop',
            ],
        ]);

        $containerBuilder->loadFromExtension('fos_rest', [
            'param_fetcher_listener' => true,
        ]);
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir().'/sonata-user-bundle/var/';
    }
}
