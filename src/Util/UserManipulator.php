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

namespace Sonata\UserBundle\Util;

use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManagerInterface;

/**
 * @internal
 */
final class UserManipulator
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function create(
        string $username,
        string $password,
        string $email,
        bool $active,
        bool $superadmin
    ): UserInterface {
        $user = $this->userManager->create();

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled($active);
        $user->setSuperAdmin($superadmin);

        $this->userManager->save($user);

        return $user;
    }

    public function activate(string $username): void
    {
        $user = $this->findUserByUsernameOrThrowException($username);

        $user->setEnabled(true);

        $this->userManager->save($user);
    }

    public function deactivate(string $username): void
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setEnabled(false);

        $this->userManager->save($user);
    }

    public function changePassword(string $username, string $password): void
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setPlainPassword($password);

        $this->userManager->save($user);
    }

    public function promote(string $username): void
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setSuperAdmin(true);

        $this->userManager->save($user);
    }

    public function demote(string $username): void
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setSuperAdmin(false);

        $this->userManager->save($user);
    }

    public function addRole(string $username, string $role): bool
    {
        $user = $this->findUserByUsernameOrThrowException($username);

        if ($user->hasRole($role)) {
            return false;
        }

        $user->addRole($role);

        $this->userManager->save($user);

        return true;
    }

    public function removeRole(string $username, string $role): bool
    {
        $user = $this->findUserByUsernameOrThrowException($username);

        if (!$user->hasRole($role)) {
            return false;
        }

        $user->removeRole($role);

        $this->userManager->save($user);

        return true;
    }

    /**
     * @throws \InvalidArgumentException When user does not exist
     */
    private function findUserByUsernameOrThrowException(string $username): UserInterface
    {
        $user = $this->userManager->findUserByUsername($username);

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }

        return $user;
    }
}
