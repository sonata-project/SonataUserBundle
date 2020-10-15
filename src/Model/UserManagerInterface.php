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

use Sonata\DatagridBundle\Pager\PageableInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
interface UserManagerInterface extends PageableInterface
{
    /**
     * Alias for the repository method.
     *
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return UserInterface[]
     */
    public function findUsersBy(?array $criteria = null, ?array $orderBy = null, $limit = null, $offset = null);

    /**
     * Creates an empty user instance.
     *
     * @return UserInterface
     */
    public function createUser();

    /**
     * Deletes a user.
     */
    public function deleteUser(UserInterface $user);

    /**
     * Finds one user by the given criteria.
     *
     * @return UserInterface|null
     */
    public function findUserBy(array $criteria);

    /**
     * Find a user by its username.
     *
     * @param string $username
     *
     * @return UserInterface|null
     */
    public function findUserByUsername($username);

    /**
     * Finds a user by its email.
     *
     * @param string $email
     *
     * @return UserInterface|null
     */
    public function findUserByEmail($email);

    /**
     * Finds a user by its username or email.
     *
     * @param string $usernameOrEmail
     *
     * @return UserInterface|null
     */
    public function findUserByUsernameOrEmail($usernameOrEmail);

    /**
     * Finds a user by its confirmationToken.
     *
     * @param string $token
     *
     * @return UserInterface|null
     */
    public function findUserByConfirmationToken($token);

    /**
     * Returns a collection with all user instances.
     *
     * @return \Traversable
     */
    public function findUsers();

    /**
     * Returns the user's fully qualified class name.
     *
     * @return string
     */
    public function getClass();

    /**
     * Reloads a user.
     */
    public function reloadUser(UserInterface $user);

    /**
     * Updates a user.
     */
    public function updateUser(UserInterface $user);

    /**
     * Updates the canonical username and email fields for a user.
     */
    public function updateCanonicalFields(UserInterface $user);

    /**
     * Updates a user password if a plain password is set.
     */
    public function updatePassword(UserInterface $user);
}
