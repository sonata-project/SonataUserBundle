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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Model\UserManagerInterface;
use Sonata\UserBundle\Security\UserProvider;
use Sonata\UserBundle\Tests\App\Entity\User;
use Sonata\UserBundle\Tests\Entity\User as EntityUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserProviderTest extends TestCase
{
    /**
     * @var MockObject&UserManagerInterface
     */
    private MockObject $userManager;

    private UserProvider $userProvider;

    protected function setUp(): void
    {
        $this->userManager = $this->createMock(UserManagerInterface::class);

        $this->userProvider = new UserProvider($this->userManager);
    }

    public function testLoadUserByUsername(): void
    {
        $user = new User();
        $user->setEnabled(true);

        $this->userManager->expects(static::once())
            ->method('findUserByUsernameOrEmail')
            ->with('foobar')
            ->willReturn($user);

        static::assertSame($user, $this->userProvider->loadUserByUsername('foobar'));
    }

    /**
     * TODO: Simplify exception expectation when dropping support for Symfony 4.4.
     *
     * @psalm-suppress UndefinedClass, PossiblyInvalidArgument
     */
    public function testLoadUserByInvalidUsername(): void
    {
        $this->userManager->expects(static::once())->method('findUserByUsernameOrEmail');

        // @phpstan-ignore-next-line
        $this->expectException(class_exists(UserNotFoundException::class) ? UserNotFoundException::class : UsernameNotFoundException::class);

        $this->userProvider->loadUserByUsername('foobar');
    }

    public function testRefreshUserBy(): void
    {
        $user = new User();
        $user->setId(123);

        $refreshedUser = new User();

        $this->userManager->expects(static::once())
            ->method('findOneBy')
            ->with(['id' => 123])
            ->willReturn($refreshedUser);
        $this->userManager->expects(static::atLeastOnce())
            ->method('getClass')
            ->willReturn(\get_class($user));

        static::assertSame($refreshedUser, $this->userProvider->refreshUser($user));
    }

    /**
     * TODO: Simplify exception expectation when dropping support for Symfony 4.4.
     *
     * @psalm-suppress UndefinedClass, PossiblyInvalidArgument
     */
    public function testRefreshDeleted(): void
    {
        $user = new User();

        $this->userManager->expects(static::once())->method('findOneBy');
        $this->userManager->expects(static::atLeastOnce())
            ->method('getClass')
            ->willReturn(\get_class($user));

        // @phpstan-ignore-next-line
        $this->expectException(class_exists(UserNotFoundException::class) ? UserNotFoundException::class : UsernameNotFoundException::class);

        $this->userProvider->refreshUser($user);
    }

    public function testRefreshInvalidUser(): void
    {
        $user = $this->createStub(UserInterface::class);

        $this->expectException(UnsupportedUserException::class);

        $this->userProvider->refreshUser($user);
    }

    public function testRefreshInvalidUserClass(): void
    {
        $user = new User();
        $providedUser = new EntityUser();

        $this->userManager->expects(static::atLeastOnce())
            ->method('getClass')
            ->willReturn(\get_class($user));

        $this->expectException(UnsupportedUserException::class);

        $this->userProvider->refreshUser($providedUser);
    }
}
