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

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\UserBundle\Action\LoginAction;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginActionTest extends TestCase
{
    /**
     * @var EngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templating;

    /**
     * @var UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlGenerator;

    /**
     * @var AuthorizationCheckerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorizationChecker;

    /**
     * @var Pool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pool;

    /**
     * @var TemplateRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateRegistry;

    /**
     * @var TokenStorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tokenStorage;

    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var CsrfTokenManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $csrfTokenManager;

    public function setUp(): void
    {
        $this->templating = $this->createMock(EngineInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->pool = $this->createMock(Pool::class);
        $this->templateRegistry = $this->createMock(TemplateRegistryInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->session = $this->createMock(Session::class);
        $this->csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
    }

    public function testAlreadyAuthenticated(): void
    {
        $request = new Request();

        $user = $this->createMock(UserInterface::class);

        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($user);

        $this->tokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($token);

        $bag = $this->createMock(FlashBag::class);
        $bag->expects($this->once())
            ->method('add')
            ->with('sonata_user_error', 'sonata_user_already_authenticated');

        $this->session->expects($this->any())
            ->method('getFlashBag')
            ->willReturn($bag);

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('sonata_admin_dashboard')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/foo', $result->getTargetUrl());
    }

    /**
     * @dataProvider userGrantedAdminProvider
     */
    public function testUserGrantedAdmin(string $referer, string $expectedRedirectUrl): void
    {
        $session = $this->createMock(Session::class);
        $request = Request::create('http://some.url.com/exact-request-uri');
        $request->server->add(['HTTP_REFERER' => $referer]);
        $request->setSession($session);

        $this->tokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn(null);

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('sonata_admin_dashboard')
            ->willReturn('/foo');

        $this->authorizationChecker->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN')
            ->willReturn(true);

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame($expectedRedirectUrl, $result->getTargetUrl());
    }

    public function userGrantedAdminProvider(): array
    {
        return [
            ['', '/foo'],
            ['http://some.url.com/exact-request-uri', '/foo'],
            ['http://some.url.com', 'http://some.url.com'],
        ];
    }

    /**
     * @dataProvider unauthenticatedProvider
     */
    public function testUnauthenticated(string $lastUsername, AuthenticationException $errorMessage = null): void
    {
        $session = $this->createMock(Session::class);
        $sessionParameters = [
            '_security.last_error' => $errorMessage,
            '_security.last_username' => $lastUsername,
        ];
        $session->expects($this->any())
            ->method('get')
            ->willReturnCallback(function ($key) use ($sessionParameters) {
                return $sessionParameters[$key] ?? null;
            });
        $session->expects($this->any())
            ->method('has')
            ->willReturnCallback(function ($key) use ($sessionParameters) {
                return isset($sessionParameters[$key]);
            });
        $request = new Request();
        $request->setSession($session);

        $response = $this->createMock(Response::class);

        $parameters = [
            'admin_pool' => $this->pool,
            'base_template' => 'base.html.twig',
            'csrf_token' => 'csrf-token',
            'error' => $errorMessage,
            'last_username' => $lastUsername,
            'reset_route' => '/foo',
        ];

        $csrfToken = $this->createMock(CsrfToken::class);
        $csrfToken->expects($this->any())
            ->method('getValue')
            ->willReturn('csrf-token');

        $this->tokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn(null);

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('sonata_user_admin_resetting_request')
            ->willReturn('/foo');

        $this->authorizationChecker->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN')
            ->willReturn(false);

        $this->csrfTokenManager->expects($this->any())
            ->method('getToken')
            ->with('authenticate')
            ->willReturn($csrfToken);

        $this->templateRegistry->expects($this->any())
            ->method('getTemplate')
            ->with('layout')
            ->willReturn('base.html.twig');

        $this->templating->expects($this->any())
            ->method('renderResponse')
            ->with('@SonataUser/Admin/Security/login.html.twig', $parameters)
            ->willReturn($response);

        $action = $this->getAction();
        $result = $action($request);

        $this->assertSame($response, $result);
    }

    public function unauthenticatedProvider(): array
    {
        $error = new AuthenticationException('An error');

        return [
            ['', null],
            ['FooUser', $error],
        ];
    }

    private function getAction(): LoginAction
    {
        $action = new LoginAction(
            $this->templating,
            $this->urlGenerator,
            $this->authorizationChecker,
            $this->pool,
            $this->templateRegistry,
            $this->tokenStorage,
            $this->session
        );
        $action->setCsrfTokenManager($this->csrfTokenManager);

        return $action;
    }
}
