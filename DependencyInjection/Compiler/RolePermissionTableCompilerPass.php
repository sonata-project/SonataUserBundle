<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RolePermissionTableCompilerPass
 * @package Sonata\UserBundle\DependencyInjection\Compiler
 */
class RolePermissionTableCompilerPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $service = $container->getDefinition('sonata.user.editable_role_builder');
        foreach ($container->findTaggedServiceIds('sonata.admin') as $name => $items) {
            foreach ($items as $k => $item) {
                if (isset($item['show_in_role_table']) && $item['show_in_role_table'] === false) {
                    $service->addMethodCall('addExclude', array($name));
                }
            }
        }
    }
}