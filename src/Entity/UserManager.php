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

namespace Sonata\UserBundle\Entity;

use Sonata\Doctrine\Entity\BaseEntityManager;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManagerInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class UserManager extends BaseEntityManager implements UserManagerInterface
{
    public function findUserByUsername(string $username): ?UserInterface
    {
        return $this->findOneBy(['usernameCanonical' => $username]);
    }

    public function findUserByEmail(string $email): ?UserInterface
    {
        return $this->findOneBy(['emailCanonical' => $email]);
    }

    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?UserInterface
    {
        if (preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->findUserByEmail($usernameOrEmail);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    public function findUserByConfirmationToken(string $token): ?UserInterface
    {
        return $this->findOneBy(['confirmationToken' => $token]);
    }
}
