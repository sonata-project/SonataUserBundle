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
use Sonata\UserBundle\Action\CheckEmailAction;
use Sonata\UserBundle\Action\RequestAction;
use Sonata\UserBundle\Action\ResetAction;
use Sonata\UserBundle\Action\SendEmailAction;
use Sonata\UserBundle\Controller\AdminResettingController;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminResettingControllerTest extends TestCase
{
    /**
     * @var RequestStack|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestStack;

    /**
     * @var TestAction
     */
    protected $testAction;
    /**
     * @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerBuilder::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->testAction = new TestAction();

        $services = [
            CheckEmailAction::class => $this->testAction,
            RequestAction::class => $this->testAction,
            ResetAction::class => $this->testAction,
            SendEmailAction::class => $this->testAction,
            'request_stack' => $this->requestStack,
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

    public function getController(): AdminResettingController
    {
        $controller = new AdminResettingController();
        $controller->setContainer($this->container);

        return $controller;
    }

    /**
     * @group legacy
     * @expectedDeprecation The Sonata\UserBundle\Controller\AdminResettingController class is deprecated since version 4.x and will be removed in 5.0. Use Sonata\UserBundle\Controller\RequestAction, Sonata\UserBundle\Controller\CheckEmailAction, Sonata\UserBundle\Controller\ResetAction or Sonata\UserBundle\Controller\SendEmailAction instead.
     */
    public function testCheckEmailAction(): void
    {
        $request = new Request();

        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $controller = $this->getController();
        $result = $controller->checkEmailAction($request);

        $this->assertSame('ok', $result);
    }

    public function testRequestAction(): void
    {
        $request = new Request();

        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $controller = $this->getController();
        $result = $controller->requestAction();

        $this->assertSame('ok', $result);
    }

    public function testResetAction(): void
    {
        $request = new Request();

        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $controller = $this->getController();
        $result = $controller->resetAction($request, 'foo');

        $this->assertSame('ok', $result);
    }

    public function testSendEmailAction(): void
    {
        $request = new Request();

        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $controller = $this->getController();
        $result = $controller->sendEmailAction($request);

        $this->assertSame('ok', $result);
    }
}

final class TestAction
{
    public function __invoke()
    {
        return 'ok';
    }
}
