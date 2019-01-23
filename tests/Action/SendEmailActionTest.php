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

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\UserBundle\Action\SendEmailAction;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SendEmailActionTest extends TestCase
{
    /**
     * @var UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlGenerator;

    /**
     * @var Pool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pool;

    /**
     * @var TemplateRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateRegistry;

    /**
     * @var UserManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userManager;

    /**
     * @var MailerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mailer;

    /**
     * @var TokenGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tokenGenerator;

    /**
     * @var int
     */
    protected $resetTtl;

    /**
     * @var string
     */
    protected $fromEmail;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    /**
     * @var EngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templating;

    public function setUp(): void
    {
        $this->templating = $this->createMock(EngineInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->pool = $this->createMock(Pool::class);
        $this->templateRegistry = $this->createMock(TemplateRegistryInterface::class);
        $this->userManager = $this->createMock(UserManagerInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $this->resetTtl = 60;
        $this->fromEmail = 'noreply@sonata-project.org';
        $this->template = 'email.txt.twig';
        $this->container = $this->createMock(ContainerBuilder::class);
    }

    public function testUnknownUsername(): void
    {
        $request = new Request([], ['username' => 'bar']);
        $response = $this->createMock(Response::class);

        $parameters = [
            'base_template' => 'base.html.twig',
            'admin_pool' => $this->pool,
            'invalid_username' => 'bar',
        ];

        $this->templating->expects($this->once())
            ->method('renderResponse')
            ->with('@SonataUser/Admin/Security/Resetting/request.html.twig', $parameters)
            ->willReturn($response);

        $this->templateRegistry->expects($this->any())
            ->method('getTemplate')
            ->with('layout')
            ->willReturn('base.html.twig');

        $this->userManager->expects($this->any())
            ->method('findUserByUsernameOrEmail')
            ->with('bar')
            ->willReturn(null);

        $action = $this->getAction();
        $result = $action($request);

        $this->assertSame($response, $result);
    }

    public function testPasswordRequestNonExpired(): void
    {
        $request = new Request([], ['username' => 'bar']);

        $user = $this->createMock(User::class);
        $user->expects($this->any())
            ->method('isPasswordRequestNonExpired')
            ->willReturn(true);

        $this->userManager->expects($this->any())
            ->method('findUserByUsernameOrEmail')
            ->with('bar')
            ->willReturn($user);

        $this->mailer->expects($this->never())
            ->method('sendResettingEmailMessage');

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('sonata_user_admin_resetting_check_email')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/foo', $result->getTargetUrl());
    }

    public function testAccountLocked(): void
    {
        $request = new Request([], ['username' => 'bar']);

        $user = $this->createMock(User::class);
        $user->expects($this->any())
            ->method('isPasswordRequestNonExpired')
            ->willReturn(false);
        $user->expects($this->any())
            ->method('isAccountNonLocked')
            ->willReturn(false);

        $this->userManager->expects($this->any())
            ->method('findUserByUsernameOrEmail')
            ->with('bar')
            ->willReturn($user);

        $this->mailer->expects($this->never())
            ->method('sendResettingEmailMessage');

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('sonata_user_admin_resetting_request')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/foo', $result->getTargetUrl());
    }

    public function testEmailSent(): void
    {
        $request = new Request([], ['username' => 'bar']);

        $storedToken = null;

        $user = $this->createMock(User::class);
        $user->expects($this->any())
            ->method('getEmail')
            ->willReturn('user@sonata-project.org');
        $user->expects($this->any())
            ->method('isPasswordRequestNonExpired')
            ->willReturn(false);
        $user->expects($this->any())
            ->method('isAccountNonLocked')
            ->willReturn(true);
        $user->expects($this->any())
            ->method('setConfirmationToken')
            ->willReturnCallback(function ($token) use (&$storedToken): void {
                $storedToken = $token;
            });
        $user->expects($this->any())
            ->method('getConfirmationToken')
            ->willReturnCallback(function () use (&$storedToken) {
                return $storedToken;
            });

        $this->userManager->expects($this->any())
            ->method('findUserByUsernameOrEmail')
            ->with('bar')
            ->willReturn($user);

        $this->tokenGenerator->expects($this->once())
            ->method('generateToken')
            ->willReturn('user-token');

        $this->mailer->expects($this->once())
            ->method('sendResettingEmailMessage');

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->withConsecutive(
                ['sonata_user_admin_resetting_check_email', ['username' => 'bar']]
            )
            ->willReturnOnConsecutiveCalls(
                '/check-email'
            );

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/check-email', $result->getTargetUrl());
    }

    private function getAction(): SendEmailAction
    {
        return new SendEmailAction(
            $this->templating,
            $this->urlGenerator,
            $this->pool,
            $this->templateRegistry,
            $this->userManager,
            $this->mailer,
            $this->tokenGenerator,
            $this->resetTtl
        );
    }
}
