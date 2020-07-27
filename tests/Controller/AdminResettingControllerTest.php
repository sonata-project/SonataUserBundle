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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Action\CheckEmailAction;
use Sonata\UserBundle\Action\RequestAction;
use Sonata\UserBundle\Action\ResetAction;
use Sonata\UserBundle\Action\SendEmailAction;
use Sonata\UserBundle\Controller\AdminResettingController;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminResettingControllerTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @var RequestStack|MockObject
     */
    protected $requestStack;

    /**
     * @var TestAction
     */
    protected $testAction;
    /**
     * @var ContainerBuilder|MockObject
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
        $this->container
            ->method('has')
            ->willReturnCallback(static function (string $service) use ($services): bool {
                return isset($services[$service]);
            });
        $this->container
            ->method('get')
            ->willReturnCallback(static function (string $service) use ($services) {
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
     */
    public function testCheckEmailAction(): void
    {
        $request = new Request();

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->expectDeprecation(
            'The Sonata\UserBundle\Controller\AdminResettingController class is deprecated since version 4.3.0'
            .' and will be removed in 5.0. Use Sonata\UserBundle\Action\RequestAction, Sonata\UserBundle\Action\CheckEmailAction'
            .', Sonata\UserBundle\Action\ResetAction or Sonata\UserBundle\Action\SendEmailAction instead.'
        );

        $controller = $this->getController();

        $result = $controller->checkEmailAction($request);

        $this->assertSame('ok', $result);
    }

    public function testRequestAction(): void
    {
        $request = new Request();

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $controller = $this->getController();
        $result = $controller->requestAction();

        $this->assertSame('ok', $result);
    }

    public function testResetAction(): void
    {
        $request = new Request();

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $controller = $this->getController();
        $result = $controller->resetAction($request, 'foo');

        $this->assertSame('ok', $result);
    }

    public function testSendEmailAction(): void
    {
        $request = new Request();

        $this->requestStack
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
