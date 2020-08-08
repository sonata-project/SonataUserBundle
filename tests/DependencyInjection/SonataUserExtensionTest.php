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

use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\UserInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\UserBundle\DependencyInjection\Configuration;
use Sonata\UserBundle\DependencyInjection\SonataUserExtension;
use Sonata\UserBundle\Document\BaseUser;
use Sonata\UserBundle\Entity\BaseGroup;
use Sonata\UserBundle\Tests\Admin\Document\GroupAdmin;
use Sonata\UserBundle\Tests\Admin\Document\UserAdmin;
use Sonata\UserBundle\Tests\Document\Group;
use Sonata\UserBundle\Tests\Document\User;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class SonataUserExtensionTest extends AbstractExtensionTestCase
{
    use ExpectDeprecationTrait;

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
     * @group legacy
     */
    public function testLoadDefault(): void
    {
        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

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
     */
    public function testTwigConfigParameterIsSet(): void
    {
        $fakeTwigExtension = $this->getMockBuilder(TwigExtension::class)
            ->setMethods(['load', 'getAlias'])
            ->getMock();

        $fakeTwigExtension
            ->method('getAlias')
            ->willReturn('twig');

        $this->container->registerExtension($fakeTwigExtension);

        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

        $this->load();

        $twigConfigurations = $this->container->getExtensionConfig('twig');

        $this->assertArrayHasKey(0, $twigConfigurations);
        $this->assertArrayHasKey('form_themes', $twigConfigurations[0]);
        $this->assertSame(
            ['@SonataUser/Form/form_admin_fields.html.twig'],
            $twigConfigurations[0]['form_themes']
        );
    }

    /**
     * @group legacy
     */
    public function testTwigConfigParameterIsNotSet(): void
    {
        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

        $this->load();

        $twigConfigurations = $this->container->getExtensionConfig('twig');

        $this->assertArrayNotHasKey(0, $twigConfigurations);
    }

    /**
     * @group legacy
     */
    public function testCorrectModelClass(): void
    {
        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

        $this->load(['class' => ['user' => \Sonata\UserBundle\Tests\Entity\User::class]]);
    }

    /**
     * @group legacy
     */
    public function testCorrectModelClassWithLeadingSlash(): void
    {
        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

        $this->load(['class' => ['user' => \Sonata\UserBundle\Tests\Entity\User::class]]);
    }

    /**
     * @group legacy
     */
    public function testCorrectAdminClass(): void
    {
        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

        $this->load(['admin' => ['user' => ['class' => \Sonata\UserBundle\Tests\Admin\Entity\UserAdmin::class]]]);
    }

    /**
     * @group legacy
     */
    public function testCorrectModelClassWithNotDefaultManagerType(): void
    {
        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

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
     * @group legacy
     */
    public function testFosUserBundleModelClasses(): void
    {
        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

        $this->load(['manager_type' => 'orm', 'class' => [
            'user' => UserInterface::class,
            'group' => GroupInterface::class,
        ]]);
    }

    /**
     * @group legacy
     */
    public function testNotCorrespondingUserClass(): void
    {
        $this->expectException('InvalidArgumentException');

        $this->expectExceptionMessage(
            'Model class "Sonata\UserBundle\Entity\BaseUser" does not correspond to manager type "mongodb".'
        );

        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

        $this->load(['manager_type' => 'mongodb', 'class' => ['user' => \Sonata\UserBundle\Entity\BaseUser::class]]);
    }

    /**
     * @group legacy
     */
    public function testNotCorrespondingGroupClass(): void
    {
        $this->expectException('InvalidArgumentException');

        $this->expectExceptionMessage(
            'Model class "Sonata\UserBundle\Entity\BaseGroup" does not correspond to manager type "mongodb".'
        );

        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

        $this->load(['manager_type' => 'mongodb', 'class' => [
            'user' => BaseUser::class,
            'group' => BaseGroup::class,
        ]]);
    }

    /**
     * @group legacy
     */
    public function testConfigureGoogleAuthenticatorDisabled(): void
    {
        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

        $this->load(['google_authenticator' => ['enabled' => false]]);

        $this->assertContainerBuilderHasParameter('sonata.user.google.authenticator.enabled', false);
        $this->assertContainerBuilderNotHasService('sonata.user.google.authenticator');
        $this->assertContainerBuilderNotHasService('sonata.user.google.authenticator.provider');
        $this->assertContainerBuilderNotHasService('sonata.user.google.authenticator.interactive_login_listener');
        $this->assertContainerBuilderNotHasService('sonata.user.google.authenticator.request_listener');
    }

    /**
     * @group legacy
     */
    public function testConfigureGoogleAuthenticatorEnabled(): void
    {
        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

        $this->load(['google_authenticator' => ['enabled' => true, 'forced_for_role' => ['ROLE_USER'], 'trusted_ip_list' => ['0.0.0.1'],
                                                'server' => 'bar', ]]);

        $this->assertContainerBuilderHasParameter('sonata.user.google.authenticator.enabled', true);
        $this->assertContainerBuilderHasService('sonata.user.google.authenticator');
        $this->assertContainerBuilderHasService('sonata.user.google.authenticator.provider');
        $this->assertContainerBuilderHasService('sonata.user.google.authenticator.interactive_login_listener');
        $this->assertContainerBuilderHasService('sonata.user.google.authenticator.request_listener');
        $this->assertContainerBuilderHasParameter('sonata.user.google.authenticator.forced_for_role', ['ROLE_ADMIN', 'ROLE_USER']);
        $this->assertContainerBuilderHasParameter('sonata.user.google.authenticator.trusted_ip_list', ['127.0.0.1', '0.0.0.1']);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sonata.user.google.authenticator.provider', 0, 'bar');
    }

    /**
     * @group legacy
     */
    public function testMailerConfigParameterIfNotSet(): void
    {
        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

        $this->load();

        $this->assertContainerBuilderHasAlias('sonata.user.mailer', 'sonata.user.mailer.default');
    }

    /**
     * @group legacy
     */
    public function testMailerConfigParameter(): void
    {
        $this->expectDeprecation('The \'Google\Authenticator\' namespace is deprecated in sonata-project/GoogleAuthenticator since version 2.1 and will be removed in 3.0.');

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
