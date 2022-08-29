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

namespace Sonata\UserBundle\Tests\Functional\Action;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LogoutActionTest extends WebTestCase
{
    public function testItLogouts(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');
        $client->followRedirect();

        static::assertRouteSame('sonata_user_admin_security_login');
    }
}
