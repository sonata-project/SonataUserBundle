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
use Sonata\UserBundle\Security\UserChecker;

class UserCheckerTest extends TestCase
{
    public function testCheckPreAuthFailsLockedOut()
    {
        $this->expectException('\Symfony\Component\Security\Core\Exception\LockedException');
        $this->expectExceptionMessage('User account is locked.');

        $userMock = $this->getUser(false, false, false, false);
        $checker = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    public function testCheckPreAuthFailsIsEnabled()
    {
        $this->expectException('\Symfony\Component\Security\Core\Exception\DisabledException');
        $this->expectExceptionMessage('User account is disabled.');

        $userMock = $this->getUser(true, false, false, false);
        $checker = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    public function testCheckPreAuthFailsIsAccountNonExpired()
    {
        $this->expectException('\Symfony\Component\Security\Core\Exception\AccountExpiredException');
        $this->expectExceptionMessage('User account has expired.');

        $userMock = $this->getUser(true, true, false, false);
        $checker = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    public function testCheckPreAuthSuccess()
    {
        $userMock = $this->getUser(true, true, true, false);
        $checker = new UserChecker();

        try {
            $this->assertNull($checker->checkPreAuth($userMock));
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    public function testCheckPostAuthFailsIsCredentialsNonExpired()
    {
        $this->expectException('\Symfony\Component\Security\Core\Exception\CredentialsExpiredException');
        $this->expectExceptionMessage('User credentials have expired.');

        $userMock = $this->getUser(true, true, true, false);
        $checker = new UserChecker();
        $checker->checkPostAuth($userMock);
    }

    public function testCheckPostAuthSuccess()
    {
        $userMock = $this->getUser(true, true, true, true);
        $checker = new UserChecker();

        try {
            $this->assertNull($checker->checkPostAuth($userMock));
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    private function getUser($isAccountNonLocked, $isEnabled, $isAccountNonExpired, $isCredentialsNonExpired)
    {
        $userMock = $this->getMockBuilder('Sonata\UserBundle\Model\User')->getMock();
        $userMock
            ->method('isAccountNonLocked')
            ->willReturn($isAccountNonLocked);
        $userMock
            ->method('isEnabled')
            ->willReturn($isEnabled);
        $userMock
            ->method('isAccountNonExpired')
            ->willReturn($isAccountNonExpired);
        $userMock
            ->method('isCredentialsNonExpired')
            ->willReturn($isCredentialsNonExpired);

        return $userMock;
    }
}
