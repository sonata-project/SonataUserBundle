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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\UserBundle\Action\ResetAction;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    protected function setUp(): void
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
        $this->session = new Session(new MockFileSessionStorage());
        $this->resetTtl = 60;
        $this->firewallName = 'default';
    }

    public function testAuthenticated(): void
    {
        $request = new Request();

        $this->authorizationChecker->expects($this->once())
            ->method('isGranted')
            ->willReturn(true);

        $this->urlGenerator
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
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('The user with "confirmation token" does not exist for value "token"');

        $request = new Request();

        $this->userManager
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
        $user
            ->method('isPasswordRequestNonExpired')
            ->willReturn(false);

        $this->userManager
            ->method('findUserByConfirmationToken')
            ->with('token')
            ->willReturn($user);

        $this->urlGenerator
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

        $user = $this->createMock(User::class);
        $user
            ->method('isPasswordRequestNonExpired')
            ->willReturn(true);

        $form = $this->createMock(Form::class);
        $form
            ->method('isValid')
            ->willReturn(true);
        $form
            ->method('isSubmitted')
            ->willReturn(false);
        $form->expects($this->once())
            ->method('createView')
            ->willReturn('Form View');

        $this->userManager
            ->method('findUserByConfirmationToken')
            ->with('user-token')
            ->willReturn($user);

        $this->formFactory->expects($this->once())
            ->method('createForm')
            ->willReturn($form);

        $this->urlGenerator
            ->method('generate')
            ->with('sonata_admin_dashboard')
            ->willReturn('/foo');

        $this->templating
            ->method('render')
            ->with('@SonataUser/Admin/Security/Resetting/reset.html.twig', $parameters)
            ->willReturn('template content');

        $this->templateRegistry
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

        $user = $this->createMock(User::class);
        $user
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
        $form
            ->method('isValid')
            ->willReturn(true);
        $form
            ->method('isSubmitted')
            ->willReturn(true);

        $this->translator
            ->method('trans')
            ->willReturnCallback(static function (string $message): string {
                return $message;
            });

        $this->userManager
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

        $this->urlGenerator
            ->method('generate')
            ->with('sonata_admin_dashboard')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request, 'token');

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/foo', $result->getTargetUrl());
        $this->assertSame([
            'success' => ['resetting.flash.success'],
        ], $this->session->getFlashBag()->all());
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
