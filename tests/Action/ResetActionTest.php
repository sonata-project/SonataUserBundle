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

namespace Sonata\UserBundle\Tests\Action;

use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Security\LoginManagerInterface;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\UserBundle\Action\ResetAction;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ResetActionTest extends TestCase
{
    /**
     * @var AuthorizationCheckerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorizationChecker;

    /**
     * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $router;

    /**
     * @var Pool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pool;

    /**
     * @var TemplateRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateRegistry;

    /**
     * @var FactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formFactory;

    /**
     * @var UserManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userManager;

    /**
     * @var LoginManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loginManager;

    /**
     * @var TranslatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $translator;

    /**
     * @var int
     */
    protected $resetTtl;

    /**
     * @var string
     */
    protected $firewallName;

    /**
     * @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    /**
     * @var EngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templating;

    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    public function setUp(): void
    {
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->pool = $this->createMock(Pool::class);
        $this->templateRegistry = $this->createMock(TemplateRegistryInterface::class);
        $this->formFactory = $this->createMock(FactoryInterface::class);
        $this->userManager = $this->createMock(UserManagerInterface::class);
        $this->loginManager = $this->createMock(LoginManagerInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->resetTtl = 60;
        $this->firewallName = 'default';
        $this->container = $this->createMock(ContainerBuilder::class);
        $this->templating = $this->createMock(EngineInterface::class);
        $this->session = $this->createMock(Session::class);

        $services = [
            'router' => $this->router,
            'templating' => $this->templating,
            'session' => $this->session,
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

    public function testAuthenticated(): void
    {
        $request = new Request();

        $this->authorizationChecker->expects($this->once())
            ->method('isGranted')
            ->willReturn(true);

        $this->router->expects($this->any())
            ->method('generate')
            ->with('sonata_admin_dashboard')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request, 'token');

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals('/foo', $result->getTargetUrl());
    }

    public function testUnknownToken(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('The user with "confirmation token" does not exist for value "token"');

        $request = new Request();

        $this->userManager->expects($this->any())
            ->method('findUserByConfirmationToken')
            ->with('token')
            ->willReturn(null);

        $action = $this->getAction();
        $action($request, 'token');
    }

    public function testPasswordRequestNonExpired(): void
    {
        $request = new Request();

        $user = $this->createMock(User::class);
        $user->expects($this->any())
            ->method('isPasswordRequestNonExpired')
            ->willReturn(false);

        $this->userManager->expects($this->any())
            ->method('findUserByConfirmationToken')
            ->with('token')
            ->willReturn($user);

        $this->router->expects($this->any())
            ->method('generate')
            ->with('sonata_user_admin_resetting_request')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request, 'token');

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals('/foo', $result->getTargetUrl());
    }

    public function testReset(): void
    {
        $request = new Request();

        $user = $this->createMock(User::class);
        $user->expects($this->any())
            ->method('isPasswordRequestNonExpired')
            ->willReturn(true);
        $user->expects($this->once())
            ->method('setLastLogin');
        $user->expects($this->once())
            ->method('setConfirmationToken')
            ->with(null);
        $user->expects($this->once())
            ->method('setPasswordRequestedAt')
            ->with(null);
        $user->expects($this->once())
            ->method('setEnabled')
            ->with(true);

        $form = $this->createMock(Form::class);
        $form->expects($this->any())
            ->method('isValid')
            ->willReturn(true);
        $form->expects($this->any())
            ->method('isSubmitted')
            ->willReturn(true);

        $this->translator->expects($this->any())
            ->method('trans')
            ->willReturnCallback(function ($message) {
                return $message;
            });

        $bag = $this->createMock(FlashBag::class);
        $bag->expects($this->once())
            ->method('add')
            ->with('success', 'resetting.flash.success');

        $this->session->expects($this->any())
            ->method('getFlashBag')
            ->willReturn($bag);

        $this->userManager->expects($this->any())
            ->method('findUserByConfirmationToken')
            ->with('token')
            ->willReturn($user);
        $this->userManager->expects($this->once())
            ->method('updateUser')
            ->with($user);

        $this->loginManager->expects($this->once())
            ->method('logInUser')
            ->with('default', $user, $this->isInstanceOf(Response::class));

        $this->formFactory->expects($this->once())
            ->method('createForm')
            ->willReturn($form);

        $this->router->expects($this->any())
            ->method('generate')
            ->with('sonata_admin_dashboard')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request, 'token');

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals('/foo', $result->getTargetUrl());
    }

    private function getAction(): ResetAction
    {
        $action = new ResetAction(
            $this->authorizationChecker,
            $this->router,
            $this->pool,
            $this->templateRegistry,
            $this->formFactory,
            $this->userManager,
            $this->loginManager,
            $this->translator,
            $this->resetTtl,
            $this->firewallName
        );
        $action->setContainer($this->container);

        return $action;
    }
}
