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

namespace Sonata\UserBundle\Document;

use Doctrine\Persistence\ManagerRegistry;
use Sonata\Doctrine\Document\BaseDocumentManager;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use Sonata\UserBundle\Util\CanonicalFieldsUpdaterInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * @phpstan-extends BaseDocumentManager<UserInterface>
 */
final class UserManager extends BaseDocumentManager implements UserManagerInterface
{
    private CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater;

    /**
     * TODO: Simplify this once support for Symfony 4.4 is dropped.
     *
     * @psalm-suppress UndefinedDocblockClass
     * @phpstan-ignore-next-line
     *
     * @var UserPasswordEncoderInterface|UserPasswordHasherInterface
     */
    private object $userPasswordHasher;

    /**
     * TODO: Simplify this once support for Symfony 4.4 is dropped.
     *
     * @psalm-suppress UndefinedDocblockClass
     *
     * @param UserPasswordEncoderInterface|UserPasswordHasherInterface $userPasswordHasher
     *
     * @phpstan-param class-string<UserInterface> $class
     */
    public function __construct(
        string $class,
        ManagerRegistry $registry,
        CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater,
        // @phpstan-ignore-next-line
        object $userPasswordHasher
    ) {
        parent::__construct($class, $registry);

        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @psalm-suppress UndefinedDocblockClass
     */
    public function updatePassword(UserInterface $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if (null === $plainPassword) {
            return;
        }

        if ($this->userPasswordHasher instanceof UserPasswordHasherInterface) {
            $password = $this->userPasswordHasher->hashPassword($user, $plainPassword);
        } else {
            // @phpstan-ignore-next-line
            $password = $this->userPasswordHasher->encodePassword($user, $plainPassword);
        }

        $user->setPassword($password);
        $user->eraseCredentials();
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
