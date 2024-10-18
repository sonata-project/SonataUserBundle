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
use Sonata\UserBundle\Action\LoginAction;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class LoginActionTest extends TestCase
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
     * @var MockObject&AuthenticationUtils
     */
    private MockObject $authenticationUtils;

    private Pool $pool;

    /**
     * @var MockObject&TemplateRegistryInterface
     */
    private MockObject $templateRegistry;

    /**
     * @var MockObject&TokenStorageInterface
     */
    private MockObject $tokenStorage;

    /**
     * @var MockObject&Session
     */
    private MockObject $session;

    /**
     * @var MockObject&CsrfTokenManagerInterface
     */
    private MockObject $csrfTokenManager;

    /**
     * @var MockObject&TranslatorInterface
     */
    private MockObject $translator;

    protected function setUp(): void
    {
        $this->templating = $this->createMock(Environment::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $this->pool = new Pool(new Container());
        $this->templateRegistry = $this->createMock(TemplateRegistryInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->session = $this->createMock(Session::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
    }

    public function testAlreadyAuthenticated(): void
    {
        $request = new Request();
        $request->setSession($this->session);

        $user = $this->createMock(UserInterface::class);

        $token = $this->createMock(TokenInterface::class);
        $token
            ->method('getUser')
            ->willReturn($user);

        $this->tokenStorage
            ->method('getToken')
            ->willReturn($token);

        $this->translator->expects(static::once())
            ->method('trans')
            ->with('sonata_user_already_authenticated')
            ->willReturn('Already Authenticated');

        $bag = $this->createMock(FlashBag::class);
        $bag->expects(static::once())
            ->method('add')
            ->with('sonata_user_error', 'Already Authenticated');

        $this->session
            ->method('getFlashBag')
            ->willReturn($bag);

        $this->urlGenerator
            ->method('generate')
            ->with('sonata_admin_dashboard')
            ->willReturn('/foo');

        $action = $this->getAction(true);
        $result = $action($request);

        static::assertInstanceOf(RedirectResponse::class, $result);
        static::assertSame('/foo', $result->getTargetUrl());
    }

    /**
     * @dataProvider provideUnauthenticatedCases
     */
    public function testUnauthenticated(string $lastUsername, ?AuthenticationException $errorMessage = null, bool $resetting = true): void
    {
        $session = $this->createMock(Session::class);
        $sessionParameters = [
            '_security.last_error' => $errorMessage,
            '_security.last_username' => $lastUsername,
        ];
        $session
            ->method('get')
            ->willReturnCallback(
                static fn (string $key) => $sessionParameters[$key] ?? null
            );
        $session
            ->method('has')
            ->willReturnCallback(
                static fn (string $key): bool => isset($sessionParameters[$key])
            );
        $request = new Request();
        $request->setSession($session);

        $parameters = [
            'admin_pool' => $this->pool,
            'base_template' => 'base.html.twig',
            'csrf_token' => 'csrf-token',
            'error' => $errorMessage,
            'last_username' => $lastUsername,
            'reset_route' => $resetting ? '/foo' : null,
        ];

        $csrfToken = $this->createMock(CsrfToken::class);
        $csrfToken
            ->method('getValue')
            ->willReturn('csrf-token');

        $this->tokenStorage
            ->method('getToken')
            ->willReturn(null);

        $this->urlGenerator
            ->expects($resetting ? static::once() : static::never())
            ->method('generate')
            ->with('sonata_user_admin_resetting_request')
            ->willReturn('/foo');

        $this->authenticationUtils->method('getLastUsername')->willReturn($lastUsername);
        $this->authenticationUtils->method('getLastAuthenticationError')->willReturn($errorMessage);

        $this->csrfTokenManager
            ->method('getToken')
            ->with('authenticate')
            ->willReturn($csrfToken);

        $this->templateRegistry
            ->method('getTemplate')
            ->with('layout')
            ->willReturn('base.html.twig');

        $this->templating
            ->method('render')
            ->with('@SonataUser/Admin/Security/login.html.twig', $parameters)
            ->willReturn('template content');

        $action = $this->getAction($resetting);
        $result = $action($request);

        static::assertSame('template content', $result->getContent());
    }

    /**
     * @return iterable<mixed[]>
     *
     * @phpstan-return iterable<array{string, AuthenticationException|null, boolean}>
     */
    public function provideUnauthenticatedCases(): iterable
    {
        $error = new AuthenticationException('An error');
        yield ['', null, true];
        yield ['FooUser', $error, true];
        yield ['', null, false];
        yield ['FooUser', $error, false];
    }

    private function getAction(bool $resetting): LoginAction
    {
        return new LoginAction(
            $this->templating,
            $this->urlGenerator,
            $this->authenticationUtils,
            $this->pool,
            $this->templateRegistry,
            $this->tokenStorage,
            $this->translator,
            $this->csrfTokenManager,
            $resetting,
        );
    }
}
