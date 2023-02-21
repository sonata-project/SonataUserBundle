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

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class SonataUserExtensionNoResettingTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setParameter('kernel.bundles', [
            'SonataDoctrineBundle' => true,
            'SonataAdminBundle' => true,
        ]);
    }

    public function testMailerConfigParameterIfNotSet(): void
    {
        $this->load();

        $this->assertContainerBuilderNotHasService('sonata.user.mailer');
    }

    public function testMailerConfigParameter(): void
    {
        $this->load(['mailer' => 'custom.mailer.service.id']);
        $this->assertContainerBuilderNotHasService('sonata.user.mailer');
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SonataUserExtension(),
        ];
    }
}
