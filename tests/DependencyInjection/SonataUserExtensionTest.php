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

    /**
     * @group legacy
     * @expectedDeprecation The 'Google\Authenticator' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.
     */
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

    /**
     * @group legacy
     * @expectedDeprecation The 'Google\Authenticator' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.
     */
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

    /**
     * @group legacy
     * @expectedDeprecation The 'Google\Authenticator' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.
     */
    public function testTwigConfigParameterIsNotSet(): void
    {
        $this->load();

        $twigConfigurations = $this->container->getExtensionConfig('twig');

        $this->assertArrayNotHasKey(0, $twigConfigurations);
    }

    /**
     * @group legacy
     * @expectedDeprecation The 'Google\Authenticator' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.
     */
    public function testCorrectModelClass(): void
    {
        $this->load(['class' => ['user' => 'Sonata\UserBundle\Entity\BaseUser']]);
    }

    /**
     * @group legacy
     * @expectedDeprecation The 'Google\Authenticator' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.
     */
    public function testCorrectModelClassWithLeadingSlash(): void
    {
        $this->load(['class' => ['user' => '\Sonata\UserBundle\Entity\BaseUser']]);
    }

    /**
     * @group legacy
     * @expectedDeprecation The 'Google\Authenticator' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.
     */
    public function testCorrectModelClassWithNotDefaultManagerType(): void
    {
        $this->load([
            'manager_type' => 'mongodb',
            'class' => [
                'user' => 'Sonata\UserBundle\Tests\Document\User',
                'group' => 'Sonata\UserBundle\Tests\Document\Group',
            ],
        ]);
    }

    /**
     * @group legacy
     * @expectedDeprecation The 'Google\Authenticator' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.
     */
    public function testNotImplementUserInterface(): void
    {
        $this->expectExceptionMessage(
            'Class "Foo\User" should implement interface "FOS\UserBundle\Model\UserInterface"'
        );

        $this->load(['class' => ['user' => 'Foo\User', 'group' => 'Sonata\UserBundle\Entity\BaseGroup']]);
    }

    /**
     * @group legacy
     * @expectedDeprecation The 'Google\Authenticator' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.
     */
    public function testNotImplementGroupInterface(): void
    {
        $this->expectExceptionMessage(
            'Class "Foo\Group" should implement interface "FOS\UserBundle\Model\GroupInterface"'
        );

        $this->load(['class' => ['user' => 'Sonata\UserBundle\Entity\BaseUser', 'group' => 'Foo\Group']]);
    }

    /**
     * @group legacy
     * @expectedDeprecation The 'Google\Authenticator' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.
     */
    public function testCorrectImplementGroupInterface(): void
    {
        $this->load(['class' => ['user' => 'Sonata\UserBundle\Entity\BaseUser', 'group' => 'Sonata\UserBundle\Entity\BaseGroup']]);
    }

    /**
     * @group legacy
     * @expectedDeprecation The 'Google\Authenticator' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.
     */
    public function testConfigureGoogleAuthenticatorDisabled(): void
    {
        $this->load(['google_authenticator' => ['enabled' => false]]);

        $this->assertContainerBuilderHasParameter('sonata.user.google.authenticator.enabled', false);
        $this->assertContainerBuilderNotHasService('sonata.user.google.authenticator');
        $this->assertContainerBuilderNotHasService('sonata.user.google.authenticator.provider');
        $this->assertContainerBuilderNotHasService('sonata.user.google.authenticator.interactive_login_listener');
        $this->assertContainerBuilderNotHasService('sonata.user.google.authenticator.request_listener');
    }

    /**
     * @group legacy
     * @expectedDeprecation The 'Google\Authenticator' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.
     */
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
