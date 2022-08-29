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
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Tests\App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ResetActionTest extends WebTestCase
{
    public function testItRedirectsToResetPasswordRequestOnInvalidToken(): void
    {
        $client = static::createClient();
        $client->catchExceptions(true);
        $client->request('GET', '/reset/25');

        static::assertResponseStatusCodeSame(404);
    }

    public function testItSubmitsResetPasswordFormWithNonValidData(): void
    {
        $client = static::createClient();

        $user = $this->prepareData();
        $confirmationToken = $user->getConfirmationToken();
        \assert(null !== $confirmationToken);

        static::assertSame($user->getPassword(), 'random_password');

        $client->request('GET', sprintf('/reset/%s', $confirmationToken));

        static::assertResponseIsSuccessful();

        $client->submitForm('submit', [
            'resetting_form[plainPassword][first]' => 'new_password',
            'resetting_form[plainPassword][second]' => 'not_matching_password',
        ]);

        static::assertResponseIsSuccessful();
        static::assertRouteSame('sonata_user_admin_resetting_reset');
    }

    public function testItResetsPassword(): void
    {
        $client = static::createClient();

        // TODO: Remove this line when the issue gets solved: https://github.com/symfony/symfony/issues/45580
        $client->disableReboot();

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
     * @psalm-suppress UndefinedPropertyFetch
     */
    private function prepareData(): UserInterface
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
    private function refreshUser(UserInterface $user): UserInterface
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
