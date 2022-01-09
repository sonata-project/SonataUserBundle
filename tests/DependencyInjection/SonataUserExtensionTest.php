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
use Sonata\UserBundle\Admin\Entity\UserAdmin as EntityUserAdmin;
use Sonata\UserBundle\DependencyInjection\SonataUserExtension;
use Sonata\UserBundle\Entity\BaseUser as EntityBaseUser;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Tests\Admin\Document\UserAdmin as DocumentUserAdmin;
use Sonata\UserBundle\Tests\Document\User as DocumentUser;
use Sonata\UserBundle\Tests\Entity\User as EntityUser;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class SonataUserExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setParameter('kernel.bundles', [
            'SonataDoctrineBundle' => true,
            'SonataAdminBundle' => true,
        ]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testLoadDefault(): void
    {
        $this->load();
    }

    public function testImpersonatingDisabled(): void
    {
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sonata.user.twig.global', 2, false);
    }

    public function testImpersonatingEnabled(): void
    {
        $this->load([
            'impersonating' => [
                'enabled' => true,
                'route' => 'sonata_admin_dashboard',
                'parameters' => [
                    'foo' => 'bar',
                ],
            ],
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sonata.user.twig.global', 2, true);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sonata.user.twig.global', 3, 'sonata_admin_dashboard');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sonata.user.twig.global', 4, ['foo' => 'bar']);
    }

    public function testTwigConfigParameterIsSetting(): void
    {
        $fakeContainer = $this->createMock(ContainerBuilder::class);

        $fakeContainer->expects(static::once())
            ->method('hasExtension')
            ->with(static::equalTo('twig'))
            ->willReturn(true);

        $fakeContainer->expects(static::once())
            ->method('prependExtensionConfig')
            ->with('twig', ['form_themes' => ['@SonataUser/Form/form_admin_fields.html.twig']]);

        foreach ($this->getContainerExtensions() as $extension) {
            if ($extension instanceof PrependExtensionInterface) {
                $extension->prepend($fakeContainer);
            }
        }
    }

    public function testTwigConfigParameterIsSet(): void
    {
        $fakeTwigExtension = $this->createStub(TwigExtension::class);

        $fakeTwigExtension
            ->method('getAlias')
            ->willReturn('twig');

        $this->container->registerExtension($fakeTwigExtension);

        $this->load();

        $twigConfigurations = $this->container->getExtensionConfig('twig');

        static::assertArrayHasKey(0, $twigConfigurations);
        static::assertArrayHasKey('form_themes', $twigConfigurations[0]);
        static::assertSame(
            ['@SonataUser/Form/form_admin_fields.html.twig'],
            $twigConfigurations[0]['form_themes']
        );
    }

    public function testTwigConfigParameterIsNotSet(): void
    {
        $this->load();

        $twigConfigurations = $this->container->getExtensionConfig('twig');

        static::assertArrayNotHasKey(0, $twigConfigurations);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCorrectModelClass(): void
    {
        $this->load(['class' => ['user' => EntityUser::class]]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCorrectAdminClass(): void
    {
        $this->load(['admin' => ['user' => ['class' => EntityUserAdmin::class]]]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCorrectModelClassWithNotDefaultManagerType(): void
    {
        $this->load([
            'manager_type' => 'mongodb',
            'class' => [
                'user' => DocumentUser::class,
            ],
            'admin' => [
                'user' => ['class' => DocumentUserAdmin::class],
            ],
        ]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSonataUserBundleModelClasses(): void
    {
        $this->load(['manager_type' => 'orm', 'class' => [
            'user' => UserInterface::class,
        ]]);
    }

    public function testNotCorrespondingUserClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'Model class "Sonata\UserBundle\Entity\BaseUser" does not correspond to manager type "mongodb".'
        );

        $this->load(['manager_type' => 'mongodb', 'class' => ['user' => EntityBaseUser::class]]);
    }

    public function testMailerConfigParameterIfNotSet(): void
    {
        $this->load();

        $this->assertContainerBuilderHasAlias('sonata.user.mailer', 'sonata.user.mailer.default');
    }

    public function testMailerConfigParameter(): void
    {
        $this->load(['mailer' => 'custom.mailer.service.id']);

        $this->assertContainerBuilderHasAlias('sonata.user.mailer', 'custom.mailer.service.id');
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
