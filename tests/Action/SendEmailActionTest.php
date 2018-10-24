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
use Symfony\Component\Routing\RouterInterface;

class SendEmailActionTest extends TestCase
{
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
     * @var UserManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userManager;

    /**
     * @var \Swift_Mailer|\PHPUnit_Framework_MockObject_MockObject
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
        $this->router = $this->createMock(RouterInterface::class);
        $this->pool = $this->createMock(Pool::class);
        $this->templateRegistry = $this->createMock(TemplateRegistryInterface::class);
        $this->userManager = $this->createMock(UserManagerInterface::class);
        $this->mailer = $this->createMock(\Swift_Mailer::class);
        $this->tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $this->resetTtl = 60;
        $this->fromEmail = 'noreply@sonata-project.org';
        $this->template = 'email.txt.twig';
        $this->container = $this->createMock(ContainerBuilder::class);
        $this->templating = $this->createMock(EngineInterface::class);

        $services = [
            'router' => $this->router,
            'templating' => $this->templating,
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

    public function testUnknownUsername(): void
    {
        $request = new Request([], ['username' => 'bar']);

        $parameters = [
            'base_template' => 'base.html.twig',
            'admin_pool' => $this->pool,
            'invalid_username' => 'bar',
        ];

        $this->templating->expects($this->once())
            ->method('render')
            ->with('@SonataUser/Admin/Security/Resetting/request.html.twig', $parameters)
            ->willReturn('Foo Content');

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

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals('Foo Content', $result->getContent());
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
            ->method('send');

        $this->router->expects($this->any())
            ->method('generate')
            ->with('sonata_user_admin_resetting_check_email')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals('/foo', $result->getTargetUrl());
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
            ->method('send');

        $this->router->expects($this->any())
            ->method('generate')
            ->with('sonata_user_admin_resetting_request')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals('/foo', $result->getTargetUrl());
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
            ->method('send');

        $this->router->expects($this->any())
            ->method('generate')
            ->withConsecutive(
                ['sonata_user_admin_resetting_reset', ['token' => 'user-token']],
                ['sonata_user_admin_resetting_check_email', ['username' => 'bar']]
            )
            ->willReturnOnConsecutiveCalls(
                '/reset',
                '/check-email'
            );

        $parameters = [
            'user' => $user,
            'confirmationUrl' => '/reset',
        ];

        $this->templating->expects($this->once())
            ->method('render')
            ->with($this->template, $parameters)
            ->willReturn("Subject\nMail content");

        $this->mailer->expects($this->once())
            ->method('send')
            ->willReturnCallback(function (\Swift_Message $message): void {
                $this->assertEquals('Subject', $message->getSubject());
                $this->assertEquals('Mail content', $message->getBody());
                $this->assertArrayHasKey($this->fromEmail, $message->getFrom());
                $this->assertArrayHasKey('user@sonata-project.org', $message->getTo());
            });

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals('/check-email', $result->getTargetUrl());
    }

    private function getAction(): SendEmailAction
    {
        $action = new SendEmailAction(
            $this->router,
            $this->pool,
            $this->templateRegistry,
            $this->userManager,
            $this->mailer,
            $this->tokenGenerator,
            $this->resetTtl,
            [$this->fromEmail],
            $this->template
        );
        $action->setContainer($this->container);

        return $action;
    }
}
