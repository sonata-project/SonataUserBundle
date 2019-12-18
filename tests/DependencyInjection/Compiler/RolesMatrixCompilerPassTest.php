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

namespace Sonata\UserBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\DependencyInjection\Compiler\RolesMatrixCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class RolesMatrixCompilerPassTest extends TestCase
{
    public function testProcess(): void
    {
        $definition = $this->createMock(Definition::class);

        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with('sonata.user.admin_roles_builder')
            ->willReturn($definition);

        $taggedServices = [
            'sonata.admin.foo' => [0 => ['show_in_roles_matrix' => true]],
            'sonata.admin.bar' => [0 => ['show_in_roles_matrix' => false]],
            'sonata.admin.test' => [],
        ];

        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('sonata.admin')
            ->willReturn($taggedServices);

        $definition
            ->expects($this->once())
            ->method('addMethodCall')
            ->with('addExcludeAdmin', ['sonata.admin.bar']);

        $compilerPass = new RolesMatrixCompilerPass();
        $compilerPass->process($container);
    }
}
