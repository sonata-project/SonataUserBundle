<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Mailer;

use Sonata\UserBundle\Model\FOSUserInterface;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface MailerInterface
{
    /**
     * Send an email to a user to confirm the account creation.
     *
     * @param FOSUserInterface $user
     */
    public function sendConfirmationEmailMessage(FOSUserInterface $user);

    /**
     * Send an email to a user to confirm the password reset.
     *
     * @param FOSUserInterface $user
     */
    public function sendResettingEmailMessage(FOSUserInterface $user);
}
