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

use FOS\UserBundle\Model\UserManagerInterface;
use Google\Authenticator\GoogleAuthenticator;
use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Entity\BaseUser;
use Sonata\UserBundle\EventListener\TwoFactorLoginSuccessHandler;
use Sonata\UserBundle\GoogleAuthenticator\Helper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Twig\Environment;

/**
 * @author Aleksey Krichevsky <krich.al.vl@gmail.com>
 */
class TwoFactorLoginSuccessHandlerTest extends TestCase
{
    /**
     * @var TwoFactorLoginSuccessHandler
     */
    private $testClass;

    /**
     * @var BaseUser
     */
    private $user;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var UsernamePasswordToken
     */
    private $token;

    protected function tearDown(): void
    {
        $this->user = null;
        $this->token = null;
        $this->testClass = null;
        $this->request = null;
    }

    /**
     * @dataProvider data
     */
    public function testDifferentCases(?string $secret, string $role, ?string $ip, bool $needSession, string $expected): void
    {
        $this->createTestClass($secret, $role, $ip, $needSession);
        $response = $this->testClass->onAuthenticationSuccess($this->request, $this->token);
        $this->assertInstanceOf($expected, $response);
    }

    public function data(): array
    {
        return [
            [null, 'ROLE_USER', '192.168.1.1', false, Response::class],
            [null, 'ROLE_ADMIN', null, false, RedirectResponse::class],
            ['AQAOXT322JDYRKVJ', 'ROLE_ADMIN', '192.168.1.1', true, RedirectResponse::class],
            [null, 'ROLE_ADMIN', '192.168.1.1', false, Response::class],
        ];
    }

    private function createTestClass(?string $secret, string $userRole, ?string $remoteAddr, bool $needSession): void
    {
        $this->user = new BaseUser();
        if (null !== $secret) {
            $this->user->setTwoStepVerificationCode($secret);
        }
        $this->token = new UsernamePasswordToken($this->user, null, 'admin', [$userRole]);
        $tokenStorage = new TokenStorage();
        $tokenStorage->setToken($this->token);
        $authManagerMock = $this->createMock(AuthenticationManagerInterface::class);
        $roleHierarchy = new RoleHierarchy([
            'ROLE_ADMIN' => ['ROLE_USER'],
            'ROLE_USER' => [''],
        ]);
        $authChecker = new AuthorizationChecker($tokenStorage, $authManagerMock, new AccessDecisionManager([new RoleHierarchyVoter($roleHierarchy)]));
        $templateEngineMock = $this->createMock(Environment::class);
        $templateEngineMock->method('render')->willReturn('Rendered view');
        $userManagerMock = $this->createMock(UserManagerInterface::class);
        $routerMock = $this->createMock(UrlGeneratorInterface::class);
        $routerMock->method('generate')->willReturn('/admin/dashboard');
        $forcedRoles = ['ROLE_ADMIN'];
        $ipWhiteList = ['127.0.0.1'];
        $helper = new Helper('site.tld', new GoogleAuthenticator(), $authChecker, $forcedRoles, $ipWhiteList);
        $this->testClass = new TwoFactorLoginSuccessHandler(
            $templateEngineMock,
            $helper,
            $userManagerMock,
            $routerMock
        );
        $this->request = Request::create('/');
        if (null !== $remoteAddr) {
            $this->request->server->set('REMOTE_ADDR', $remoteAddr);
        }
        if ($needSession) {
            $sessionMock = $this->createMock(SessionInterface::class);
            $this->request->setSession($sessionMock);
        }
    }
}
