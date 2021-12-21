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

use Doctrine\Persistence\ManagerRegistry;
use Sonata\Doctrine\Entity\BaseEntityManager;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use Sonata\UserBundle\Util\CanonicalFieldsUpdaterInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * @phpstan-extends BaseEntityManager<UserInterface>
 */
class UserManager extends BaseEntityManager implements UserManagerInterface
{
    /**
     * @var CanonicalFieldsUpdaterInterface
     */
    private $canonicalFieldsUpdater;

    public function __construct(
        string $class,
        ManagerRegistry $registry,
        CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater
    ) {
        parent::__construct($class, $registry);

        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    public function findUserByUsername(string $username): ?UserInterface
    {
        return $this->findOneBy([
            'usernameCanonical' => $this->canonicalFieldsUpdater->canonicalizeUsername($username),
        ]);
    }

    public function findUserByEmail(string $email): ?UserInterface
    {
        return $this->findOneBy([
            'emailCanonical' => $this->canonicalFieldsUpdater->canonicalizeEmail($email),
        ]);
    }

    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?UserInterface
    {
        if (1 === preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
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
