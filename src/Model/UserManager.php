<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Model;

use Sonata\UserBundle\Util\CanonicalFieldsUpdater;
use Sonata\UserBundle\Util\PasswordUpdaterInterface;

/**
 * Abstract User Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class UserManager implements FOSUserManagerInterface
{
    private $passwordUpdater;
    private $canonicalFieldsUpdater;

    public function __construct(PasswordUpdaterInterface $passwordUpdater, CanonicalFieldsUpdater $canonicalFieldsUpdater)
    {
        $this->passwordUpdater = $passwordUpdater;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function createUser()
    {
        $class = $this->getClass();
        $user = new $class();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(array('emailCanonical' => $this->canonicalFieldsUpdater->canonicalizeEmail($email)));
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUsername($username)
    {
        return $this->findUserBy(array('usernameCanonical' => $this->canonicalFieldsUpdater->canonicalizeUsername($username)));
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->findUserByEmail($usernameOrEmail);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(array('confirmationToken' => $token));
    }

    /**
     * {@inheritdoc}
     */
    public function updateCanonicalFields(FOSUserInterface $user)
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
    }

    /**
     * {@inheritdoc}
     */
    public function updatePassword(FOSUserInterface $user)
    {
        $this->passwordUpdater->hashPassword($user);
    }

    /**
     * @return PasswordUpdaterInterface
     */
    protected function getPasswordUpdater()
    {
        return $this->passwordUpdater;
    }

    /**
     * @return CanonicalFieldsUpdater
     */
    protected function getCanonicalFieldsUpdater()
    {
        return $this->canonicalFieldsUpdater;
    }
}
