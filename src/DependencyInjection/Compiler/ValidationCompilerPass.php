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

namespace Sonata\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class ValidationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('sonata.user.manager_type')
            || !$container->hasDefinition('validator.builder')) {
            return;
        }

        $managerType = $container->getParameter('sonata.user.manager_type');
        \assert(\is_string($managerType));

        $container->getDefinition('validator.builder')->addMethodCall('addXmlMapping', [
            __DIR__.'/../../Resources/config/storage-validation/'.$managerType.'.xml',
        ]);
    }
}
