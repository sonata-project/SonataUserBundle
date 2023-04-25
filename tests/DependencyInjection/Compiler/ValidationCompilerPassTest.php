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

namespace Sonata\UserBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sonata\UserBundle\DependencyInjection\Compiler\ValidationCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Validator\ValidatorBuilder;

final class ValidationCompilerPassTest extends AbstractCompilerPassTestCase
{
    public function testProcess(): void
    {
        $this->container->setParameter('sonata.user.manager_type', 'orm');
        $this->container->setDefinition('validator.builder', new Definition(ValidatorBuilder::class));

        $this->compile();

        $filePath = (new \ReflectionClass(ValidationCompilerPass::class))->getFileName();
        \assert(\is_string($filePath));
        $compilePassDir = \dirname($filePath);

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'validator.builder',
            'addXmlMapping',
            [$compilePassDir.'/../../Resources/config/storage-validation/orm.xml']
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ValidationCompilerPass());
    }
}
