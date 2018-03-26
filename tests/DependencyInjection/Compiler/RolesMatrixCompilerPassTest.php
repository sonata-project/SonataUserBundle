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
use Sonata\UserBundle\Security\RolesMatrixBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class RolesMatrixCompilerPassTest extends TestCase
{
    /**
     * @test
     */
    public function process(): void
    {
        $definition = $this->createMock(Definition::class);

        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with(RolesMatrixBuilder::class)
            ->willReturn($definition);

        $taggedServices = [
            'sonata.admin.foo' => [0 => ['show_in_role_table' => true]],
            'sonata.admin.bar' => [0 => ['show_in_role_table' => false]],
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
            ->with('addExclude', ['sonata.admin.bar']);

        $compilerPass = new RolesMatrixCompilerPass();
        $compilerPass->process($container);
    }
}
