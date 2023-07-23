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

namespace Sonata\UserBundle\Tests\App;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\BlockBundle\Cache\HttpCacheHandler;
use Sonata\BlockBundle\SonataBlockBundle;
use Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Sonata\Form\Bridge\Symfony\SonataFormBundle;
use Sonata\Twig\Bridge\Symfony\SonataTwigBundle;
use Sonata\UserBundle\SonataUserBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new SecurityBundle(),
            new DoctrineBundle(),
            new DAMADoctrineTestBundle(),
            new SonataBlockBundle(),
            new SonataDoctrineORMAdminBundle(),
            new SonataDoctrineBundle(),
            new SonataTwigBundle(),
            new SonataFormBundle(),
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

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__.'/config/routes.yaml');
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config.yaml');

        if (!class_exists(IsGranted::class)) {
            $loader->load(__DIR__.'/config/config_sf5.yaml');
        }

        /*
         * TODO: Remove when dropping support for sonatablock 4
         */
        if (class_exists(HttpCacheHandler::class)) {
            $loader->load(__DIR__.'/config/config_sonata_block_v4.yaml');
        }

        $container->setParameter('app.base_dir', $this->getBaseDir());
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir().'/sonata-user-bundle/var/';
    }
}
