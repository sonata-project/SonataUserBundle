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

use Sonata\UserBundle\Tests\App\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RequestActionTest extends WebTestCase
{
    public function testItSubmitsResetPasswordRequestWithNonExistentUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/request');

        static::assertResponseIsSuccessful();

        $client->submitForm('submit', [
            'username' => 'email@localhost.com',
        ]);

        static::assertEmailCount(0);

        $client->followRedirect();

        static::assertResponseIsSuccessful();
        static::assertRouteSame('sonata_user_admin_resetting_check_email');
    }

    /**
     * @return class-string<\Symfony\Component\HttpKernel\KernelInterface>
     */
    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
