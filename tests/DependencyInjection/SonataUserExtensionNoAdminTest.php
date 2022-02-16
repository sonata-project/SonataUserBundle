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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\UserBundle\DependencyInjection\SonataUserExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Alexandr Zolotukhin <alex@alexandrz.com>
 */
final class SonataUserExtensionNoAdminTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setParameter('kernel.bundles', [
            'SonataDoctrineBundle' => true,
        ]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testLoadDefault(): void
    {
        $this->load();
    }

    public function testGetGlobalVariablesService(): void
    {
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sonata.user.twig.global',
            0,
            new Reference('sonata.admin.pool', ContainerInterface::NULL_ON_INVALID_REFERENCE)
        );
    }

    /**
     * @return mixed[]
     */
    protected function getMinimalConfiguration(): array
    {
        return [
            'resetting' => [
                'email' => [
                    'address' => 'sonata@localhost.com',
                    'sender_name' => 'Sonata',
                ],
            ],
        ];
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SonataUserExtension(),
        ];
    }
}
