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

namespace Sonata\UserBundle\Mailer;

use Sonata\UserBundle\Model\UserInterface;

/**
 * This mailer does nothing.
 * It is used when the 'email' configuration is not set,
 * and allows to use this bundle without swiftmailer.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NoopMailer implements MailerInterface
{
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        // nothing happens.
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {
        // nothing happens.
    }
}
