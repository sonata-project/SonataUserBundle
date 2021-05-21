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

namespace Sonata\UserBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Security\LoginManager;
use Symfony\Component\HttpFoundation\Response;

class LoginManagerTest extends TestCase
{
    public function testLogInUserWithRequestStack()
    {
        $loginManager = $this->createLoginManager('main');
        $loginManager->logInUser('main', $this->mockUser());
    }

    public function testLogInUserWithRememberMeAndRequestStack()
    {
        $response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')->getMock();

        $loginManager = $this->createLoginManager('main', $response);
        $loginManager->logInUser('main', $this->mockUser(), $response);
    }

    /**
     * @param string $firewallName
     *
     * @return LoginManager
     */
    private function createLoginManager($firewallName, ?Response $response = null)
    {
        $tokenStorage = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')->getMock();

        $tokenStorage
            ->expects($this->once())
            ->method('setToken')
            ->with($this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));

        $userChecker = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserCheckerInterface')->getMock();
        $userChecker
            ->expects($this->once())
            ->method('checkPreAuth')
            ->with($this->isInstanceOf('Sonata\UserBundle\Model\UserInterface'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();

        $sessionStrategy = $this->getMockBuilder('Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface')->getMock();
        $sessionStrategy
            ->expects($this->once())
            ->method('onAuthentication')
            ->with($request, $this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));

        $requestStack = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')->getMock();
        $requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $rememberMe = null;
        if (null !== $response) {
            $rememberMe = $this->getMockBuilder('Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface')->getMock();
            $rememberMe
                ->expects($this->once())
                ->method('loginSuccess')
                ->with($request, $response, $this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
        }

        return new LoginManager($tokenStorage, $userChecker, $sessionStrategy, $requestStack, $rememberMe);
    }

    /**
     * @return mixed
     */
    private function mockUser()
    {
        $user = $this->getMockBuilder('Sonata\UserBundle\Model\UserInterface')->getMock();
        $user
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn(['ROLE_USER']);

        return $user;
    }
}
