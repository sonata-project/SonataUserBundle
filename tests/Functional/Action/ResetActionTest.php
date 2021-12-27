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

class ResetActionTest extends WebTestCase
{
    public function testItRedirectsToResetPasswordRequestOnInvalidToken(): void
    {
        $client = static::createClient();
        $client->catchExceptions(true);
        $client->request('GET', '/reset/25');

        static::assertResponseStatusCodeSame(404);
    }

    public function testItResetsPassword(): void
    {
        $client = static::createClient();

        $user = $this->prepareData();
        $confirmationToken = $user->getConfirmationToken();
        \assert(null !== $confirmationToken);

        static::assertSame($user->getPassword(), 'random_password');

        $client->request('GET', sprintf('/reset/%s', $confirmationToken));

        $client->submitForm('submit', [
            'resetting_form[plainPassword][first]' => 'new_password',
            'resetting_form[plainPassword][second]' => 'new_password',
        ]);
        $client->followRedirect();

        static::assertRouteSame('sonata_admin_dashboard');

        $user = $this->refreshUser($user);

        static::assertNull($user->getPasswordRequestedAt());
        static::assertNull($user->getConfirmationToken());
        static::assertSame($user->getPassword(), 'new_password');
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
    private function prepareData(): User
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
        $user->setConfirmationToken('confirmation_token');
        $user->setPasswordRequestedAt(new \DateTime());
        $user->setSuperAdmin(true);
        $user->setEnabled(true);

        $manager->persist($user);
        $manager->flush();

        return $user;
    }

    /**
     * @psalm-suppress UndefinedPropertyFetch
     */
    private function refreshUser(User $user): User
    {
        // TODO: Simplify this when dropping support for Symfony 4.
        // @phpstan-ignore-next-line
        $container = method_exists(static::class, 'getContainer') ? static::getContainer() : static::$container;
        $manager = $container->get('doctrine.orm.entity_manager');
        \assert($manager instanceof EntityManagerInterface);

        $user = $manager->find(User::class, $user->getId());
        \assert(null !== $user);

        return $user;
    }
}
