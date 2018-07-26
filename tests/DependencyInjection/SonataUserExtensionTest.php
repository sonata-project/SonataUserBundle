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

        $this->setParameter('kernel.bundles', ['SonataAdminBundle' => true]);
    }

    public function testLoadDefault(): void
    {
        $this->load();

        $this->assertContainerBuilderHasAlias(
            'sonata.user.user_manager',
            'sonata.user.orm.user_manager'
        );

        $this->assertContainerBuilderHasAlias(
            'sonata.user.group_manager',
            'sonata.user.orm.group_manager'
        );
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

        $this->assertSame($expectedConfig, $extension->fixImpersonating($providedConfig));
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

        $fakeContainer->expects($this->once())
            ->method('hasExtension')
            ->with($this->equalTo('twig'))
            ->willReturn(true);

        $fakeContainer->expects($this->once())
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

        $fakeTwigExtension->expects($this->any())
            ->method('getAlias')
            ->willReturn('twig');

        $this->container->registerExtension($fakeTwigExtension);

        $this->load();

        $twigConfigurations = $this->container->getExtensionConfig('twig');

        $this->assertArrayHasKey(0, $twigConfigurations);
        $this->assertArrayHasKey('form_themes', $twigConfigurations[0]);
        $this->assertEquals(
            ['@SonataUser/Form/form_admin_fields.html.twig'],
            $twigConfigurations[0]['form_themes']
        );
    }

    public function testTwigConfigParameterIsNotSet(): void
    {
        $this->load();

        $twigConfigurations = $this->container->getExtensionConfig('twig');

        $this->assertArrayNotHasKey(0, $twigConfigurations);
    }

    public function testCorrectModelClass(): void
    {
        $this->load(['class' => ['user' => 'Sonata\UserBundle\Tests\Entity\User']]);
    }

    public function testCorrectModelClassWithLeadingSlash(): void
    {
        $this->load(['class' => ['user' => '\Sonata\UserBundle\Tests\Entity\User']]);
    }

    public function testCorrectAdminClass(): void
    {
        $this->load(['admin' => ['user' => ['class' => '\Sonata\UserBundle\Tests\Admin\Entity\UserAdmin']]]);
    }

    public function testCorrectModelClassWithNotDefaultManagerType(): void
    {
        $this->load([
            'manager_type' => 'mongodb',
            'class' => [
                'user' => 'Sonata\UserBundle\Tests\Document\User',
                'group' => 'Sonata\UserBundle\Tests\Document\Group',
            ],
            'admin' => [
                'user' => ['class' => 'Sonata\UserBundle\Tests\Admin\Document\UserAdmin'],
                'group' => ['class' => 'Sonata\UserBundle\Tests\Admin\Document\GroupAdmin'],
            ],
        ]);
    }

    public function testIncorrectModelClass(): void
    {
        $this->expectException('InvalidArgumentException');

        $this->expectExceptionMessage('Model class "Foo\User" does not correspond to manager type "orm".');

        $this->load(['class' => ['user' => 'Foo\User']]);
    }

    public function testNotCorrespondingModelClass(): void
    {
        $this->expectException('InvalidArgumentException');

        $this->expectExceptionMessage(
            'Model class "Sonata\UserBundle\Admin\Entity\UserAdmin" does not correspond to manager type "mongodb".'
        );

        $this->load(['manager_type' => 'mongodb', 'class' => ['user' => 'Sonata\UserBundle\Admin\Entity\UserAdmin']]);
    }

    public function testConfigureGoogleAuthenticatorDisabled(): void
    {
        $this->load(['google_authenticator' => ['enabled' => false]]);

        $this->assertContainerBuilderHasParameter('sonata.user.google.authenticator.enabled', false);
        $this->assertContainerBuilderNotHasService('sonata.user.google.authenticator');
        $this->assertContainerBuilderNotHasService('sonata.user.google.authenticator.provider');
        $this->assertContainerBuilderNotHasService('sonata.user.google.authenticator.interactive_login_listener');
        $this->assertContainerBuilderNotHasService('sonata.user.google.authenticator.request_listener');
    }

    public function testConfigureGoogleAuthenticatorEnabled(): void
    {
        $this->load(['google_authenticator' => ['enabled' => true, 'forced_for_role' => ['ROLE_USER'], 'ip_white_list' => ['0.0.0.1'],
                                                'server' => 'bar', ]]);

        $this->assertContainerBuilderHasParameter('sonata.user.google.authenticator.enabled', true);
        $this->assertContainerBuilderHasService('sonata.user.google.authenticator');
        $this->assertContainerBuilderHasService('sonata.user.google.authenticator.provider');
        $this->assertContainerBuilderHasService('sonata.user.google.authenticator.interactive_login_listener');
        $this->assertContainerBuilderHasService('sonata.user.google.authenticator.request_listener');
        $this->assertContainerBuilderHasParameter('sonata.user.google.authenticator.forced_for_role', ['ROLE_ADMIN', 'ROLE_USER']);
        $this->assertContainerBuilderHasParameter('sonata.user.google.authenticator.ip_white_list', ['127.0.0.1', '0.0.0.1']);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sonata.user.google.authenticator.provider', 0, 'bar');
    }

    /**
     * {@inheritdoc}
     */
    protected function getMinimalConfiguration()
    {
        return (new Processor())->process((new Configuration())->getConfigTreeBuilder()->buildTree(), []);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [
            new SonataUserExtension(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function load(array $configurationValues = []): void
    {
        $configs = [$this->getMinimalConfiguration(), $configurationValues];

        foreach ($this->container->getExtensions() as $extension) {
            if ($extension instanceof PrependExtensionInterface) {
                $extension->prepend($this->container);
            }

            $extension->load($configs, $this->container);
        }
    }
}
