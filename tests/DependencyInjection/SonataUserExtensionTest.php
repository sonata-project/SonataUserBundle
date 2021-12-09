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
use Sonata\UserBundle\DependencyInjection\Configuration;
use Sonata\UserBundle\DependencyInjection\SonataUserExtension;
use Sonata\UserBundle\Document\BaseUser;
use Sonata\UserBundle\Entity\BaseGroup;
use Sonata\UserBundle\Model\GroupInterface;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Tests\Admin\Document\GroupAdmin;
use Sonata\UserBundle\Tests\Admin\Document\UserAdmin;
use Sonata\UserBundle\Tests\Document\Group;
use Sonata\UserBundle\Tests\Document\User;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class SonataUserExtensionTest extends AbstractExtensionTestCase
{
    /**
     * {@inheritdoc}
     */
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

    public function testFixImpersonatingWithWrongConfig(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('you can\'t have `impersonating` and `impersonating_route` keys defined at the same time');

        $this->load(['impersonating' => ['route' => 'foo'], 'impersonating_route' => 'bar']);
    }

    /**
     * @dataProvider fixImpersonatingDataProvider
     */
    public function testFixImpersonatingWithImpersonatingConfig(array $expectedConfig, array $providedConfig): void
    {
        $extension = new SonataUserExtension();

        static::assertSame($expectedConfig, $extension->fixImpersonating($providedConfig));
    }

    public function fixImpersonatingDataProvider(): \Generator
    {
        yield 'with impersonating with route' => [['impersonating' => ['route' => 'foo', 'parameters' => []]], ['impersonating' => ['route' => 'foo', 'parameters' => []]]];
        yield 'with impersonating without route' => [['impersonating' => false], ['impersonating' => ['parameters' => []]]];
        yield 'with impersonating_route' => [['impersonating_route' => 'foo', 'impersonating' => ['route' => 'foo', 'parameters' => []]], ['impersonating_route' => 'foo']];
    }

    public function testTwigConfigParameterIsSetting(): void
    {
        $fakeContainer = $this->getMockBuilder(ContainerBuilder::class)
            ->setMethods(['hasExtension', 'prependExtensionConfig'])
            ->getMock();

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
        $fakeTwigExtension = $this->getMockBuilder(TwigExtension::class)
            ->setMethods(['load', 'getAlias'])
            ->getMock();

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
        $this->load(['class' => ['user' => \Sonata\UserBundle\Tests\Entity\User::class]]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCorrectModelClassWithLeadingSlash(): void
    {
        $this->load(['class' => ['user' => \Sonata\UserBundle\Tests\Entity\User::class]]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCorrectAdminClass(): void
    {
        $this->load(['admin' => ['user' => ['class' => \Sonata\UserBundle\Tests\Admin\Entity\UserAdmin::class]]]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCorrectModelClassWithNotDefaultManagerType(): void
    {
        $this->load([
            'manager_type' => 'mongodb',
            'class' => [
                'user' => User::class,
                'group' => Group::class,
            ],
            'admin' => [
                'user' => ['class' => UserAdmin::class],
                'group' => ['class' => GroupAdmin::class],
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
            'group' => GroupInterface::class,
        ]]);
    }

    public function testNotCorrespondingUserClass(): void
    {
        $this->expectException('InvalidArgumentException');

        $this->expectExceptionMessage(
            'Model class "Sonata\UserBundle\Entity\BaseUser" does not correspond to manager type "mongodb".'
        );

        $this->load(['manager_type' => 'mongodb', 'class' => ['user' => \Sonata\UserBundle\Entity\BaseUser::class]]);
    }

    public function testNotCorrespondingGroupClass(): void
    {
        $this->expectException('InvalidArgumentException');

        $this->expectExceptionMessage(
            'Model class "Sonata\UserBundle\Entity\BaseGroup" does not correspond to manager type "mongodb".'
        );

        $this->load(['manager_type' => 'mongodb', 'class' => [
            'user' => BaseUser::class,
            'group' => BaseGroup::class,
        ]]);
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
     * {@inheritdoc}
     */
    protected function getMinimalConfiguration(): array
    {
        return (new Processor())->process((new Configuration())->getConfigTreeBuilder()->buildTree(), []);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [
            new SonataUserExtension(),
        ];
    }
}
