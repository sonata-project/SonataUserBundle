<?php

namespace Sonata\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


class RolePermissionTableCompilerPass implements CompilerPassInterface {
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