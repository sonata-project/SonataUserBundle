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

namespace Sonata\UserBundle\Model;

use Sonata\Doctrine\Model\ManagerInterface;

/**
 * @phpstan-extends ManagerInterface<\Sonata\UserBundle\Model\UserInterface>
 */
interface UserManagerInterface extends ManagerInterface
{
    // public function createUser(): UserInterface;

    // public function deleteUser(UserInterface $user): void;

    // public function findUserBy(array $criteria): ?UserInterface;

    public function findUserByUsername(string $username): ?UserInterface;

    public function findUserByEmail(string $email): ?UserInterface;

    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?UserInterface;

    public function findUserByConfirmationToken(string $token): ?UserInterface;

    // public function findUsers(): iterable;

    // /**
    //  * @phpstan-return class-string<UserInterface>
    //  */
    // public function getClass(): string;

    // public function reloadUser(UserInterface $user): void;

    // public function updateUser(UserInterface $user): void;

    // public function updateCanonicalFields(UserInterface $user): void;

    // public function updatePassword(UserInterface $user): void;
}
