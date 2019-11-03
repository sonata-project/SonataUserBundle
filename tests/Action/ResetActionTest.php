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

use Sonata\UserBundle\Form\Factory\FactoryInterface;
use Sonata\UserBundle\Model\FOSUser;
use Sonata\UserBundle\Model\UserManagerInterface;
use Sonata\UserBundle\Security\LoginManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\UserBundle\Action\ResetAction;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class ResetActionTest extends TestCase
{
    /**
     * @var Environment|MockObject
     */
    protected $templating;

    /**
     * @var UrlGeneratorInterface|MockObject
     */
    protected $urlGenerator;

    /**
     * @var AuthorizationCheckerInterface|MockObject
     */
    protected $authorizationChecker;

    /**
     * @var Pool|MockObject
     */
    protected $pool;

    /**
     * @var TemplateRegistryInterface|MockObject
     */
    protected $templateRegistry;

    /**
     * @var FactoryInterface|MockObject
     */
    protected $formFactory;

    /**
     * @var UserManagerInterface|MockObject
     */
    protected $userManager;

    /**
     * @var LoginManagerInterface|MockObject
     */
    protected $loginManager;

    /**
     * @var TranslatorInterface|MockObject
     */
    protected $translator;

    /**
     * @var Session|MockObject
     */
    protected $session;

    /**
     * @var int
     */
    protected $resetTtl;

    /**
     * @var string
     */
    protected $firewallName;

    public function setUp(): void
    {
        $this->templating = $this->createMock(Environment::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->pool = $this->createMock(Pool::class);
        $this->templateRegistry = $this->createMock(TemplateRegistryInterface::class);
        $this->formFactory = $this->createMock(FactoryInterface::class);
        $this->userManager = $this->createMock(UserManagerInterface::class);
        $this->loginManager = $this->createMock(LoginManagerInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->session = $this->createMock(Session::class);
        $this->resetTtl = 60;
        $this->firewallName = 'default';
    }

    public function testAuthenticated(): void
    {
        $request = new Request();

        $this->authorizationChecker->expects($this->once())
            ->method('isGranted')
            ->willReturn(true);

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('sonata_admin_dashboard')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request, 'token');

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/foo', $result->getTargetUrl());
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

        $user = $this->createMock(FOSUser::class);
        $user->expects($this->any())
            ->method('isPasswordRequestNonExpired')
            ->willReturn(false);

        $this->userManager->expects($this->any())
            ->method('findUserByConfirmationToken')
            ->with('token')
            ->willReturn($user);

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('sonata_user_admin_resetting_request')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request, 'token');

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/foo', $result->getTargetUrl());
    }

    public function testReset(): void
    {
        $request = new Request();

        $parameters = [
            'token' => 'user-token',
            'form' => 'Form View',
            'base_template' => 'base.html.twig',
            'admin_pool' => $this->pool,
        ];

        $user = $this->createMock(FOSUser::class);
        $user->expects($this->any())
            ->method('isPasswordRequestNonExpired')
            ->willReturn(true);

        $form = $this->createMock(Form::class);
        $form->expects($this->any())
            ->method('isValid')
            ->willReturn(true);
        $form->expects($this->any())
            ->method('isSubmitted')
            ->willReturn(false);
        $form->expects($this->once())
            ->method('createView')
            ->willReturn('Form View');

        $this->userManager->expects($this->any())
            ->method('findUserByConfirmationToken')
            ->with('user-token')
            ->willReturn($user);

        $this->formFactory->expects($this->once())
            ->method('createForm')
            ->willReturn($form);

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('sonata_admin_dashboard')
            ->willReturn('/foo');

        $this->templating->expects($this->any())
            ->method('render')
            ->with('@SonataUser/Admin/Security/Resetting/reset.html.twig', $parameters)
            ->willReturn('template content');

        $this->templateRegistry->expects($this->any())
            ->method('getTemplate')
            ->with('layout')
            ->willReturn('base.html.twig');

        $action = $this->getAction();
        $result = $action($request, 'user-token');

        $this->assertSame('template content', $result->getContent());
    }

    public function testPostedReset(): void
    {
        $request = new Request();

        $user = $this->createMock(FOSUser::class);
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
            ->willReturnCallback(static function ($message) {
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

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('sonata_admin_dashboard')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request, 'token');

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/foo', $result->getTargetUrl());
    }

    private function getAction(): ResetAction
    {
        return new ResetAction(
            $this->templating,
            $this->urlGenerator,
            $this->authorizationChecker,
            $this->pool,
            $this->templateRegistry,
            $this->formFactory,
            $this->userManager,
            $this->loginManager,
            $this->translator,
            $this->session,
            $this->resetTtl,
            $this->firewallName
        );
    }
}
