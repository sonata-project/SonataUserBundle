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
use Sonata\UserBundle\Security\EmailProvider;
use Sonata\UserBundle\Security\UserProvider;

class EmailProviderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $userManager;

    /**
     * @var UserProvider
     */
    private $userProvider;

    protected function setUp(): void
    {
        $this->userManager = $this->getMockBuilder('Sonata\UserBundle\Model\UserManagerInterface')->getMock();
        $this->userProvider = new EmailProvider($this->userManager);
    }

    public function testLoadUserByUsername()
    {
        $user = $this->getMockBuilder('Sonata\UserBundle\Model\UserInterface')->getMock();
        $this->userManager->expects($this->once())
            ->method('findUserByEmail')
            ->with('foobar')
            ->willReturn($user);

        $this->assertSame($user, $this->userProvider->loadUserByUsername('foobar'));
    }

    public function testLoadUserByInvalidUsername()
    {
        $this->expectException('\Symfony\Component\Security\Core\Exception\UsernameNotFoundException');

        $this->userManager->expects($this->once())
            ->method('findUserByEmail')
            ->with('foobar')
            ->willReturn(null);

        $this->userProvider->loadUserByUsername('foobar');
    }

    public function testRefreshUserBy()
    {
        $user = $this->getMockBuilder('Sonata\UserBundle\Model\User')
            ->setMethods(['getId'])
            ->getMock();

        $user->expects($this->once())
            ->method('getId')
            ->willReturn('123');

        $refreshedUser = $this->getMockBuilder('Sonata\UserBundle\Model\UserInterface')->getMock();
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with(['id' => '123'])
            ->willReturn($refreshedUser);

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->willReturn(\get_class($user));

        $this->assertSame($refreshedUser, $this->userProvider->refreshUser($user));
    }

    public function testRefreshDeleted()
    {
        $this->expectException('\Symfony\Component\Security\Core\Exception\UsernameNotFoundException');

        $user = $this->getMockForAbstractClass('Sonata\UserBundle\Model\User');
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->willReturn(null);

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->willReturn(\get_class($user));

        $this->userProvider->refreshUser($user);
    }

    public function testRefreshInvalidUser()
    {
        $this->expectException('\Symfony\Component\Security\Core\Exception\UnsupportedUserException');

        $user = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')->getMock();
        $this->userManager->expects($this->any())
            ->method('getClass')
            ->willReturn(\get_class($user));

        $this->userProvider->refreshUser($user);
    }

    public function testRefreshInvalidUserClass()
    {
        $this->expectException('\Symfony\Component\Security\Core\Exception\UnsupportedUserException');

        $user = $this->getMockBuilder('Sonata\UserBundle\Model\User')->getMock();
        $providedUser = $this->getMockBuilder('Sonata\UserBundle\Tests\Entity\User')->getMock();

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->willReturn(\get_class($user));

        $this->userProvider->refreshUser($providedUser);
    }
}
