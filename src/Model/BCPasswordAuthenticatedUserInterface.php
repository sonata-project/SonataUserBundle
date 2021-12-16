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

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/* TODO: Simplify this when dropping support for Symfony 4 */
if (interface_exists(PasswordAuthenticatedUserInterface::class)) {
    /** @psalm-suppress UnrecognizedStatement */
    interface BCPasswordAuthenticatedUserInterface extends PasswordAuthenticatedUserInterface
    {
    }
} else {
    /** @psalm-suppress UnrecognizedStatement */
    interface BCPasswordAuthenticatedUserInterface
    {
    }
}
