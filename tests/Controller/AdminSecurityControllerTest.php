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

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Action\CheckLoginAction;
use Sonata\UserBundle\Action\LoginAction;
use Sonata\UserBundle\Action\LogoutAction;
use Sonata\UserBundle\Controller\AdminSecurityController;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminSecurityControllerTest extends TestCase
{
    /**
     * @var TestSecurityAction
     */
    private $testAction;

    /**
     * @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerBuilder::class);
        $this->testAction = new TestSecurityAction();

        $services = [
            CheckLoginAction::class => $this->testAction,
            LoginAction::class => $this->testAction,
            LogoutAction::class => $this->testAction,
        ];
        $this->container->expects($this->any())
            ->method('has')
            ->willReturnCallback(function ($service) use ($services) {
                return isset($services[$service]);
            });
        $this->container->expects($this->any())
            ->method('get')
            ->willReturnCallback(function ($service) use ($services) {
                return $services[$service] ?? null;
            });
    }

    public function getController(): AdminSecurityController
    {
        $controller = new AdminSecurityController();
        $controller->setContainer($this->container);

        return $controller;
    }

    /**
     * @group legacy
     * @expectedDeprecation The Sonata\UserBundle\Controller\AdminSecurityController class is deprecated since version 4.x and will be removed in 5.0. Use Sonata\UserBundle\Controller\CheckLoginAction, Sonata\UserBundle\Controller\LoginAction or Sonata\UserBundle\Controller\LogoutAction instead.
     */
    public function testLogoutAction(): void
    {
        $controller = $this->getController();
        $controller->logoutAction();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCheckLoginAction(): void
    {
        $controller = $this->getController();
        $controller->checkAction();
    }

    public function testLoginAction(): void
    {
        $request = new Request();

        $controller = $this->getController();
        $result = $controller->loginAction($request);

        $this->assertInstanceOf(Response::class, $result);
    }
}

final class TestSecurityAction
{
    public function __invoke()
    {
        return new Response();
    }
}
