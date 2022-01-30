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
use Sonata\UserBundle\Action\SendEmailAction;
use Sonata\UserBundle\Mailer\MailerInterface;
use Sonata\UserBundle\Model\User;
use Sonata\UserBundle\Model\UserManagerInterface;
use Sonata\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SendEmailActionTest extends TestCase
{
    /**
     * @var MockObject&UrlGeneratorInterface
     */
    private MockObject $urlGenerator;

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
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->userManager = $this->createMock(UserManagerInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $this->resetTtl = 60;
    }

    public function testUnknownUsername(): void
    {
        $request = new Request([], ['username' => 'bar']);

        $this->userManager
            ->method('findUserByUsernameOrEmail')
            ->with('bar')
            ->willReturn(null);

        $this->mailer->expects(static::never())
            ->method('sendResettingEmailMessage');

        $this->urlGenerator
            ->method('generate')
            ->with('sonata_user_admin_resetting_check_email')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request);

        static::assertInstanceOf(RedirectResponse::class, $result);
        static::assertSame('/foo', $result->getTargetUrl());
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

        $this->mailer->expects(static::never())
            ->method('sendResettingEmailMessage');

        $this->urlGenerator
            ->method('generate')
            ->with('sonata_user_admin_resetting_check_email')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request);

        static::assertInstanceOf(RedirectResponse::class, $result);
        static::assertSame('/foo', $result->getTargetUrl());
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

        $this->mailer->expects(static::never())
            ->method('sendResettingEmailMessage');

        $this->urlGenerator
            ->method('generate')
            ->with('sonata_user_admin_resetting_check_email')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request);

        static::assertInstanceOf(RedirectResponse::class, $result);
        static::assertSame('/foo', $result->getTargetUrl());
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
        $user
            ->method('isEnabled')
            ->willReturn(true);

        $this->userManager
            ->method('findUserByUsernameOrEmail')
            ->with('bar')
            ->willReturn($user);

        $this->tokenGenerator->expects(static::once())
            ->method('generateToken')
            ->willReturn('user-token');

        $this->mailer->expects(static::once())
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

        static::assertInstanceOf(RedirectResponse::class, $result);
        static::assertSame('/check-email', $result->getTargetUrl());
    }

    private function getAction(): SendEmailAction
    {
        return new SendEmailAction(
            $this->urlGenerator,
            $this->userManager,
            $this->mailer,
            $this->tokenGenerator,
            $this->resetTtl
        );
    }
}
