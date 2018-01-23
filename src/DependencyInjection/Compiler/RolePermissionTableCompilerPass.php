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
final class RolePermissionTableCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $service = $container->getDefinition('sonata.user.editable_role_builder');

        foreach ($container->findTaggedServiceIds('sonata.admin') as $name => $items) {
            foreach ($items as $item) {
                if (isset($item['show_in_role_table']) && false === $item['show_in_role_table']) {
                    $service->addMethodCall('addExclude', [$name]);
                }
            }
        }
    }
}
