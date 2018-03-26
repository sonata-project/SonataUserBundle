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
 * @author Christian Gripp <mail@core23.de>
 * @author Cengizhan Çalışkan <cengizhancaliskan@gmail.com>
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class RolesMatrixCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('sonata.user.roles_matrix_builder');

        foreach ($container->findTaggedServiceIds('sonata.admin') as $name => $items) {
            foreach ($items as $item) {
                if (isset($item['show_in_roles_matrix']) && false === $item['show_in_roles_matrix']) {
                    $definition->addMethodCall('addExclude', [$name]);
                }
            }
        }
    }
}
