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

use Doctrine\ORM\EntityManagerInterface;
use Sonata\UserBundle\Tests\App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LoginActionTest extends WebTestCase
{
    public function testItSubmitsLoginForm(): void
    {
        $client = static::createClient();

        $this->prepareData();

        $client->request('GET', '/login');

        static::assertResponseIsSuccessful();

        $client->submitForm('submit', [
            '_username' => 'username',
            '_password' => 'random_password',
        ]);
        $client->followRedirect();

        static::assertRouteSame('sonata_admin_dashboard');

        $client->request('GET', '/login');
        $client->followRedirect();

        static::assertRouteSame('sonata_admin_dashboard');
    }

    public function testItSubmitsLoginFormWithDisabledUser(): void
    {
        $client = static::createClient();

        $this->prepareData(false);

        $client->request('GET', '/login');

        static::assertResponseIsSuccessful();

        $client->submitForm('submit', [
            '_username' => 'email@localhost.com',
            '_password' => 'random_password',
        ]);
        $client->followRedirect();

        static::assertRouteSame('sonata_user_admin_security_login');
    }

    private function prepareData(bool $enabled = true): void
    {
        $manager = static::getContainer()->get('doctrine.orm.entity_manager');
        \assert($manager instanceof EntityManagerInterface);

        $user = new User();
        $user->setUsername('username');
        $user->setEmail('email@localhost.com');
        $user->setPlainPassword('random_password');
        $user->setSuperAdmin(true);
        $user->setEnabled($enabled);

        $manager->persist($user);
        $manager->flush();
    }
}
