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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\UserBundle\Action\RequestAction;
use Sonata\UserBundle\Form\Type\ResetPasswordRequestFormType;
use Sonata\UserBundle\Mailer\MailerInterface;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use Sonata\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

final class RequestActionTest extends TestCase
{
    /**
     * @var MockObject&Environment
     */
    private MockObject $templating;

    /**
     * @var MockObject&UrlGeneratorInterface
     */
    private MockObject $urlGenerator;

    /**
     * @var MockObject&AuthorizationCheckerInterface
     */
    private MockObject $authorizationChecker;

    private Pool $pool;

    /**
     * @var MockObject&TemplateRegistryInterface
     */
    private MockObject $templateRegistry;

    /**
     * @var MockObject&FormFactoryInterface
     */
    private MockObject $formFactory;

    /**
     * @var MockObject&UserManagerInterface
     */
    private MockObject $userManager;

    /**
     * @var MockObject&MailerInterface
     */
    private MockObject $mailer;

    /**
     * @var MockObject&TokenGeneratorInterface
     */
    private MockObject $tokenGenerator;

    private int $resetTtl;

    protected function setUp(): void
    {
        $this->templating = $this->createMock(Environment::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->pool = new Pool(new Container());
        $this->templateRegistry = $this->createMock(TemplateRegistryInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->userManager = $this->createMock(UserManagerInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $this->resetTtl = 60;
    }

    public function testAuthenticated(): void
    {
        $this->authorizationChecker->expects(static::once())->method('isGranted')->willReturn(true);
        $this->urlGenerator->method('generate')->with('sonata_admin_dashboard')->willReturn('/foo');

        $action = $this->getAction();
        $result = $action(new Request());

        static::assertInstanceOf(RedirectResponse::class, $result);
        static::assertSame('/foo', $result->getTargetUrl());
    }

    public function testUnauthenticated(): void
    {
        $form = $this->createStub(FormInterface::class);
        $formView = $this->createStub(FormView::class);
        $form->method('createView')->willReturn($formView);

        $parameters = [
            'base_template' => 'base.html.twig',
            'admin_pool' => $this->pool,
            'form' => $formView,
        ];

        $this->authorizationChecker->expects(static::once())->method('isGranted')->willReturn(false);
        $this->formFactory->method('create')->with(ResetPasswordRequestFormType::class)->willReturn($form);
        $this->templateRegistry->method('getTemplate')->with('layout')->willReturn('base.html.twig');
        $this->templating->expects(static::once())->method('render')
            ->with('@SonataUser/Admin/Security/Resetting/request.html.twig', $parameters)
            ->willReturn('template content');

        $action = $this->getAction();
        $result = $action(new Request());

        static::assertSame('template content', $result->getContent());
    }

    public function testUnknownUsername(): void
    {
        $usernameForm = $this->createStub(FormInterface::class);
        $form = $this->createStub(FormInterface::class);

        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);
        $form->method('get')->willReturn($usernameForm);
        $usernameForm->method('getData')->willReturn('bar');

        $this->formFactory->method('create')->with(ResetPasswordRequestFormType::class)->willReturn($form);
        $this->userManager->expects(static::once())->method('findUserByUsernameOrEmail')->with('bar')->willReturn(null);
        $this->mailer->expects(static::never())->method('sendResettingEmailMessage');
        $this->urlGenerator->method('generate')->with('sonata_user_admin_resetting_check_email')->willReturn('/foo');

        $action = $this->getAction();
        $result = $action(new Request());

        static::assertInstanceOf(RedirectResponse::class, $result);
        static::assertSame('/foo', $result->getTargetUrl());
    }

    public function testPasswordRequestNonExpired(): void
    {
        $usernameForm = $this->createStub(FormInterface::class);
        $form = $this->createStub(FormInterface::class);

        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);
        $form->method('get')->willReturn($usernameForm);
        $usernameForm->method('getData')->willReturn('bar');

        $user = $this->createMock(UserInterface::class);
        $user->method('isPasswordRequestNonExpired')->willReturn(true);

        $this->formFactory->method('create')->with(ResetPasswordRequestFormType::class)->willReturn($form);
        $this->userManager->expects(static::once())->method('findUserByUsernameOrEmail')->with('bar')
            ->willReturn($user);
        $this->mailer->expects(static::never())->method('sendResettingEmailMessage');
        $this->urlGenerator->method('generate')->with('sonata_user_admin_resetting_check_email')->willReturn('/foo');

        $action = $this->getAction();
        $result = $action(new Request());

        static::assertInstanceOf(RedirectResponse::class, $result);
        static::assertSame('/foo', $result->getTargetUrl());
    }

    public function testAccountLocked(): void
    {
        $usernameForm = $this->createStub(FormInterface::class);
        $form = $this->createStub(FormInterface::class);

        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);
        $form->method('get')->willReturn($usernameForm);
        $usernameForm->method('getData')->willReturn('bar');

        $user = $this->createMock(UserInterface::class);
        $user->method('isPasswordRequestNonExpired')->willReturn(false);
        $user->method('isAccountNonLocked')->willReturn(false);

        $this->formFactory->method('create')->with(ResetPasswordRequestFormType::class)->willReturn($form);
        $this->userManager->expects(static::once())->method('findUserByUsernameOrEmail')->with('bar')
            ->willReturn($user);
        $this->mailer->expects(static::never())->method('sendResettingEmailMessage');
        $this->urlGenerator->method('generate')->with('sonata_user_admin_resetting_check_email')->willReturn('/foo');

        $action = $this->getAction();
        $result = $action(new Request());

        static::assertInstanceOf(RedirectResponse::class, $result);
        static::assertSame('/foo', $result->getTargetUrl());
    }

    public function testEmailSent(): void
    {
        $storedToken = null;

        $usernameForm = $this->createStub(FormInterface::class);
        $form = $this->createStub(FormInterface::class);

        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);
        $form->method('get')->willReturn($usernameForm);
        $usernameForm->method('getData')->willReturn('bar');

        $user = $this->createMock(UserInterface::class);
        $user->method('getEmail')->willReturn('user@sonata-project.org');
        $user->method('isPasswordRequestNonExpired')->willReturn(false);
        $user->method('isAccountNonLocked')->willReturn(true);
        $user->method('setConfirmationToken')
            ->willReturnCallback(static function (?string $token) use (&$storedToken): void {
                $storedToken = $token;
            });
        $user->method('getConfirmationToken')
            ->willReturnCallback(static function () use (&$storedToken): ?string {
                return $storedToken;
            });
        $user->method('isEnabled')->willReturn(true);

        $this->formFactory->method('create')->with(ResetPasswordRequestFormType::class)->willReturn($form);
        $this->userManager->method('findUserByUsernameOrEmail')->with('bar')->willReturn($user);
        $this->tokenGenerator->expects(static::once())->method('generateToken')->willReturn('user-token');
        $this->mailer->expects(static::once())->method('sendResettingEmailMessage');

        $this->urlGenerator->method('generate')->with(
            'sonata_user_admin_resetting_check_email',
            ['username' => 'bar'],
        )->willReturn('/check-email');

        $action = $this->getAction();
        $result = $action(new Request());

        static::assertInstanceOf(RedirectResponse::class, $result);
        static::assertSame('/check-email', $result->getTargetUrl());
    }

    private function getAction(): RequestAction
    {
        return new RequestAction(
            $this->templating,
            $this->urlGenerator,
            $this->authorizationChecker,
            $this->pool,
            $this->templateRegistry,
            $this->formFactory,
            $this->userManager,
            $this->mailer,
            $this->tokenGenerator,
            $this->resetTtl
        );
    }
}
