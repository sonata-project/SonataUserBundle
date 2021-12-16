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

namespace Sonata\UserBundle\Tests\Util;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Tests\App\Entity\User;
use Sonata\UserBundle\Util\CanonicalFieldsUpdater;

final class CanonicalFieldsUpdaterTest extends TestCase
{
    public function testUpdateCanonicalFields(): void
    {
        $user = new User();
        $user->setUsername('Username');
        $user->setEmail('User@Example.com');

        $updater = new CanonicalFieldsUpdater();
        $updater->updateCanonicalFields($user);

        static::assertSame('username', $user->getUsernameCanonical());
        static::assertSame('user@example.com', $user->getEmailCanonical());
    }
}
