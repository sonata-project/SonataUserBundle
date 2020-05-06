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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\UserBundle\Action\SendEmailAction;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class SendEmailActionTest extends TestCase
{
    /**
     * @var UrlGeneratorInterface|MockObject
     */
    protected $urlGenerator;

    /**
     * @var Pool|MockObject
     */
    protected $pool;

    /**
     * @var TemplateRegistryInterface|MockObject
     */
    protected $templateRegistry;

    /**
     * @var UserManagerInterface|MockObject
     */
    protected $userManager;

    /**
     * @var MailerInterface|MockObject
     */
    protected $mailer;

    /**
     * @var TokenGeneratorInterface|MockObject
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
     * @var ContainerBuilder|MockObject
     */
    protected $container;

    /**
     * @var Environment|MockObject
     */
    protected $templating;

    protected function setUp(): void
    {
        $this->templating = $this->createMock(Environment::class);
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

        $parameters = [
            'base_template' => 'base.html.twig',
            'admin_pool' => $this->pool,
            'invalid_username' => 'bar',
        ];

        $this->userManager
            ->method('findUserByUsernameOrEmail')
            ->with('bar')
            ->willReturn(null);

        $this->mailer->expects($this->never())
            ->method('sendResettingEmailMessage');

        $this->urlGenerator
            ->method('generate')
            ->with('sonata_user_admin_resetting_check_email')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/foo', $result->getTargetUrl());
    }

    public function testPasswordRequestNonExpired(): void
    {
        $request = new Request([], ['username' => 'bar']);

        $user = $this->createMock(User::class);
        $user
            ->method('isPasswordRequestNonExpired')
            ->willReturn(true);

        $this->userManager
            ->method('findUserByUsernameOrEmail')
            ->with('bar')
            ->willReturn($user);

        $this->mailer->expects($this->never())
            ->method('sendResettingEmailMessage');

        $this->urlGenerator
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
        $user
            ->method('isPasswordRequestNonExpired')
            ->willReturn(false);
        $user
            ->method('isAccountNonLocked')
            ->willReturn(false);

        $this->userManager
            ->method('findUserByUsernameOrEmail')
            ->with('bar')
            ->willReturn($user);

        $this->mailer->expects($this->never())
            ->method('sendResettingEmailMessage');

        $this->urlGenerator
            ->method('generate')
            ->with('sonata_user_admin_resetting_check_email')
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
        $user
            ->method('getEmail')
            ->willReturn('user@sonata-project.org');
        $user
            ->method('isPasswordRequestNonExpired')
            ->willReturn(false);
        $user
            ->method('isAccountNonLocked')
            ->willReturn(true);
        $user
            ->method('setConfirmationToken')
            ->willReturnCallback(static function (?string $token) use (&$storedToken): void {
                $storedToken = $token;
            });
        $user
            ->method('getConfirmationToken')
            ->willReturnCallback(static function () use (&$storedToken): ?string {
                return $storedToken;
            });

        $this->userManager
            ->method('findUserByUsernameOrEmail')
            ->with('bar')
            ->willReturn($user);

        $this->tokenGenerator->expects($this->once())
            ->method('generateToken')
            ->willReturn('user-token');

        $this->mailer->expects($this->once())
            ->method('sendResettingEmailMessage');

        $this->urlGenerator
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
