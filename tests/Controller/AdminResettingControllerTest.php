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

namespace Sonata\UserBundle\Tests\Controller;

use FOS\UserBundle\Model\UserManager;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\UserBundle\Controller\AdminResettingController;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;

class AdminResettingControllerTest extends TestCase
{
    private $controller;

    private $container;

    private $twig;

    private $userManager;

    private $adminPool;

    protected function setUp(): void
    {
        $this->controller = new AdminResettingController();
        $this->container = $this->createMock(ContainerBuilder::class);
        $this->twig = $this->createMock(TwigEngine::class);
        $this->userManager = $this->createMock(UserManager::class);
        $this->adminPool = $this->createMock(Pool::class);
    }

    public function testItIsInstantiable(): void
    {
        $this->assertNotNull($this->controller);
    }

    public function testIfUsernameNotFound(): void
    {
        $this->controller->setContainer($this->container);

        $request = new Request();
        $request->request = new ParameterBag(['username' => 'foo']);

        $this->container->expects($this->at(0))
            ->method('get')
            ->with('fos_user.user_manager')
            ->willReturn($this->userManager);

        $this->userManager->expects($this->once())
            ->method('findUserByUsernameOrEmail')
            ->with('foo')
            ->willReturn(null);

        $this->adminPool->expects($this->once())
            ->method('getTemplate')
            ->with('layout')
            ->willReturn('@SonataAdmin/standard_layout.html.twig');

        $this->container->expects($this->at(1))
            ->method('get')
            ->with('sonata.admin.pool')
            ->willReturn($this->adminPool);

        $this->container->expects($this->once())
            ->method('has')
            ->with('templating')
            ->willReturn(true);

        $this->container->expects($this->at(3))
            ->method('get')
            ->with('templating')
            ->willReturn($this->twig);

        $this->twig->expects($this->once())
            ->method(Kernel::VERSION_ID < 30000 ? 'renderResponse' : 'render')
            ->with('@SonataUser/Admin/Security/Resetting/request.html.twig', [
                'base_template' => '@SonataAdmin/standard_layout.html.twig',
                'admin_pool' => $this->adminPool,
                'invalid_username' => 'foo',
            ])
            ->willReturn('success')
        ;

        $this->controller->sendEmailAction($request);
    }
}
