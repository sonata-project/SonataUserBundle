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

final class RequestActionTest extends WebTestCase
{
    public function testItSubmitsResetPasswordRequestWithNonValidData(): void
    {
        $client = static::createClient();
        $client->request('GET', '/request');

        static::assertResponseIsSuccessful();

        $client->submitForm('submit');

        static::assertResponseIsSuccessful();
        static::assertRouteSame('sonata_user_admin_resetting_request');
    }

    public function testItSubmitsResetPasswordRequestWithNonExistentUser(): void
    {
        $client = static::createClient();

        $client->request('GET', '/request');

        static::assertResponseIsSuccessful();

        $client->submitForm('submit', [
            'reset_password_request_form[username]' => 'email@localhost.com',
        ]);

        static::assertEmailCount(0);

        $client->followRedirect();

        static::assertResponseIsSuccessful();
        static::assertRouteSame('sonata_user_admin_resetting_check_email');
    }

    public function testItSubmitsResetPasswordRequest(): void
    {
        $client = static::createClient();

        $this->prepareData();

        $client->request('GET', '/request');

        static::assertResponseIsSuccessful();

        $client->submitForm('submit', [
            'reset_password_request_form[username]' => 'email@localhost.com',
        ]);

        static::assertEmailCount(1);

        $mail = static::getMailerMessage();

        static::assertNotNull($mail);
        static::assertEmailHtmlBodyContains($mail, 'To reset your password - please visit');
        static::assertEmailAddressContains($mail, 'to', 'email@localhost.com');

        $client->followRedirect();

        static::assertResponseIsSuccessful();
        static::assertRouteSame('sonata_user_admin_resetting_check_email');
    }

    private function prepareData(): void
    {
        $manager = static::getContainer()->get('doctrine.orm.entity_manager');
        \assert($manager instanceof EntityManagerInterface);

        $user = new User();
        $user->setUsername('username');
        $user->setEmail('email@localhost.com');
        $user->setPlainPassword('random_password');
        $user->setSuperAdmin(true);
        $user->setEnabled(true);

        $manager->persist($user);
        $manager->flush();
    }
}
