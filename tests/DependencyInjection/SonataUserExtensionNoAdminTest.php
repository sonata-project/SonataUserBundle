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
use Sonata\UserBundle\Twig\GlobalVariables;

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
        // Prevent unused service from being removed by making it public
        $this->container->getDefinition('sonata.user.twig.global')->setPublic(true);
        $this->compile();

        // Make sure there's no exceptions thrown, as the service has a dependency on Admin
        // which must be optional
        $globals = $this->container->get('sonata.user.twig.global');
        $this->assertInstanceOf(GlobalVariables::class, $globals);
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
