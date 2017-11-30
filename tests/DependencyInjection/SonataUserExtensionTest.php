<?php

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
    protected function setUp()
    {
        parent::setUp();

        $this->setParameter('kernel.bundles', ['SonataAdminBundle' => true]);
    }

    public function testLoadDefault()
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

    public function testTwigConfigParameterIsSetting()
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
            ->with('twig', ['form_themes' => ['SonataUserBundle:Form:form_admin_fields.html.twig']]);

        foreach ($this->getContainerExtensions() as $extension) {
            if ($extension instanceof PrependExtensionInterface) {
                $extension->prepend($fakeContainer);
            }
        }
    }

    public function testTwigConfigParameterIsSet()
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
            ['SonataUserBundle:Form:form_admin_fields.html.twig'],
            $twigConfigurations[0]['form_themes']
        );
    }

    public function testTwigConfigParameterIsNotSet()
    {
        $this->load();

        $twigConfigurations = $this->container->getExtensionConfig('twig');

        $this->assertArrayNotHasKey(0, $twigConfigurations);
    }

    public function testCorrectModelClass()
    {
        $this->load(['class' => ['user' => 'Sonata\UserBundle\Tests\Entity\User']]);
    }

    public function testCorrectModelClassWithLeadingSlash()
    {
        $this->load(['class' => ['user' => '\Sonata\UserBundle\Tests\Entity\User']]);
    }

    public function testCorrectAdminClass()
    {
        $this->load(['admin' => ['user' => ['class' => '\Sonata\UserBundle\Tests\Admin\Entity\UserAdmin']]]);
    }

    public function testCorrectModelClassWithNotDefaultManagerType()
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

    public function testIncorrectModelClass()
    {
        $this->expectException('InvalidArgumentException');

        $this->expectExceptionMessage('Model class "Foo\User" does not correspond to manager type "orm".');

        $this->load(['class' => ['user' => 'Foo\User']]);
    }

    public function testNotCorrespondingModelClass()
    {
        $this->expectException('InvalidArgumentException');

        $this->expectExceptionMessage(
            'Model class "Sonata\UserBundle\Admin\Entity\UserAdmin" does not correspond to manager type "mongodb".'
        );

        $this->load(['manager_type' => 'mongodb', 'class' => ['user' => 'Sonata\UserBundle\Admin\Entity\UserAdmin']]);
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
    protected function load(array $configurationValues = [])
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
