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
use Sonata\UserBundle\Action\CheckLoginAction;
use Sonata\UserBundle\Action\LoginAction;
use Sonata\UserBundle\Action\LogoutAction;
use Sonata\UserBundle\Controller\AdminSecurityController;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminSecurityControllerTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @var TestSecurityAction
     */
    private $testAction;

    /**
     * @var ContainerBuilder|MockObject
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

    public function getController(): AdminSecurityController
    {
        $controller = new AdminSecurityController();
        $controller->setContainer($this->container);

        return $controller;
    }

    /**
     * @group legacy
     */
    public function testLogoutAction(): void
    {
        $this->expectDeprecation(
            'The Sonata\UserBundle\Controller\AdminSecurityController class is deprecated since version 4.3.0'
            .' and will be removed in 5.0. Use Sonata\UserBundle\Action\CheckLoginAction, Sonata\UserBundle\Action\LoginAction'
            .' or Sonata\UserBundle\Action\LogoutAction instead.'
        );

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

    /**
     * @doesNotPerformAssertions
     */
    public function testLoginAction(): void
    {
        $request = new Request();

        $controller = $this->getController();
        $result = $controller->loginAction($request);
    }
}

final class TestSecurityAction
{
    public function __invoke()
    {
        return new Response();
    }
}
