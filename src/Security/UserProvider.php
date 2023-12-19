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

namespace Sonata\UserBundle\Security;

use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @phpstan-implements UserProviderInterface<UserInterface>
 */
final class UserProvider implements UserProviderInterface
{
    public function __construct(private UserManagerInterface $userManager)
    {
    }

    /**
     * @param string $username
     */
    public function loadUserByUsername($username): SecurityUserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): SecurityUserInterface
    {
        $user = $this->findUser($identifier);

        if (null === $user || !$user->isEnabled()) {
            throw new UserNotFoundException(sprintf('Username "%s" does not exist.', $identifier));
        }

        return $user;
    }

    public function refreshUser(SecurityUserInterface $user): SecurityUserInterface
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', UserInterface::class, $user::class));
        }

        if (!$this->supportsClass($user::class)) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', $this->userManager->getClass(), $user::class));
        }

        if (null === $reloadedUser = $this->userManager->findOneBy(['id' => $user->getId()])) {
            throw new UserNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId() ?? ''));
        }

        return $reloadedUser;
    }

    /**
     * @param string $class
     */
    public function supportsClass($class): bool
    {
        $userClass = $this->userManager->getClass();

        return $userClass === $class || is_subclass_of($class, $userClass);
    }

    private function findUser(string $username): ?UserInterface
    {
        return $this->userManager->findUserByUsernameOrEmail($username);
    }
}
