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
use Sonata\UserBundle\Tests\App\AppKernel;
use Sonata\UserBundle\Tests\App\Entity\User;
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

    /** @test */
    public function itSubmitsResetPasswordRequest(): void
    {
        $client = static::createClient();

        $this->prepareData();

        $client->request('GET', '/request');

        static::assertResponseIsSuccessful();

        $client->submitForm('submit', [
            'username' => 'email@localhost.com',
        ]);

        static::assertEmailCount(1);
        static::assertRouteSame('sonata_user_admin_resetting_send_email');

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

    /**
     * @psalm-suppress UndefinedPropertyFetch
     */
    private function prepareData(): void
    {
        // TODO: Simplify this when dropping support for Symfony 4.
        // @phpstan-ignore-next-line
        $container = method_exists(static::class, 'getContainer') ? static::getContainer() : static::$container;
        $manager = $container->get('doctrine.orm.entity_manager');
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
