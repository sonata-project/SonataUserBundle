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

namespace Sonata\UserBundle\Tests\Functional\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Tests\App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class UserAdminTest extends WebTestCase
{
    /**
     * @dataProvider provideCrudUrlsCases
     */
    public function testCrudUrls(string $url): void
    {
        $client = self::createClient();

        $this->prepareData();

        $client->request('GET', $url);

        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<string[]>
     *
     * @phpstan-return iterable<array{string}>
     */
    public static function provideCrudUrlsCases(): iterable
    {
        yield 'List User' => ['/admin/tests/app/user/list'];
        yield 'Create User' => ['/admin/tests/app/user/create'];
        yield 'Edit User' => ['/admin/tests/app/user/1/edit'];
        yield 'Delete User' => ['/admin/tests/app/user/1/delete'];
    }

    /**
     * @dataProvider provideFormUrlsCases
     *
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $fieldValues
     */
    public function testFormsUrls(string $url, array $parameters, string $button, array $fieldValues = []): void
    {
        $client = self::createClient();

        $this->prepareData();

        $client->request('GET', $url, $parameters);
        $client->submitForm($button, $fieldValues);
        $client->followRedirect();

        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<array<string|array<string, mixed>>>
     *
     * @phpstan-return iterable<array{0: string, 1: array<string, mixed>, 2: string, 3?: array<string, mixed>}>
     */
    public static function provideFormUrlsCases(): iterable
    {
        yield 'Create User' => ['/admin/tests/app/user/create', [
            'uniqid' => 'user',
        ], 'btn_create_and_list', [
            'user[username]' => 'another-user',
            'user[email]' => 'another-email@localhost.com',
            'user[plainPassword]' => 'password',
            'user[enabled]' => true,
        ]];

        yield 'Edit User' => ['/admin/tests/app/user/1/edit', [], 'btn_update_and_list'];
        yield 'Remove User' => ['/admin/tests/app/user/1/delete', [], 'btn_delete'];
    }

    public function testUpdatePassword(): void
    {
        $client = self::createClient();

        // TODO: Remove this line when the issue gets solved: https://github.com/symfony/symfony/issues/45580
        $client->disableReboot();

        $user = $this->prepareData();

        static::assertSame('random_password', $user->getPassword());

        $client->request('GET', '/admin/tests/app/user/1/edit', [
            'uniqid' => 'user',
        ]);
        $client->submitForm('btn_update_and_list', [
            'user[enabled]' => false
            'user[plainPassword]' => 'new_password',
        ]);
        $client->followRedirect();

        $user = $this->refreshUser($user);

        self::assertResponseIsSuccessful();
        static::assertSame('new_password', $user->getPassword());
    }

    public function testRoleMatrixExcludedDefaultRoleIsNotVisible(): void
    {
        $client = self::createClient();

        // TODO: Remove this line when the issue gets solved: https://github.com/symfony/symfony/issues/45580
        $client->disableReboot();

        $user = $this->prepareData();
        $token = $this->loginUser($user, $client);

        static::assertSame([UserInterface::ROLE_SUPER_ADMIN, UserInterface::ROLE_DEFAULT], $user->getRoles());
        static::assertSame([UserInterface::ROLE_SUPER_ADMIN, UserInterface::ROLE_DEFAULT], $token->getRoleNames());

        $client->request('GET', '/admin/tests/app/user/1/edit', [
            'uniqid' => 'user',
        ]);

        self::assertResponseIsSuccessful();
        static::assertStringContainsString(UserInterface::ROLE_SUPER_ADMIN, (string) $client->getResponse()->getContent());
        static::assertStringNotContainsString(UserInterface::ROLE_DEFAULT, (string) $client->getResponse()->getContent());
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
        $user->setSuperAdmin(true);
        $user->setEnabled(true);

        $manager->persist($user);

        $manager->flush();
        $manager->clear();

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

    private function loginUser(UserInterface $user, KernelBrowser $client): TokenInterface
    {
        // TODO: Simplify this when dropping support for Symfony 4.
        /** @psalm-suppress UndefinedPropertyFetch */
        // @phpstan-ignore-next-line
        $container = method_exists(static::class, 'getContainer') ? static::getContainer() : static::$container;
        $tokenStorage = $container->get('security.token_storage');
        \assert($tokenStorage instanceof TokenStorageInterface);

        /** @psalm-suppress UndefinedPropertyFetch, TooManyArguments, NullArgument, InvalidArgument, UnusedPsalmSuppress */
        // @phpstan-ignore-next-line
        $token = method_exists(UsernamePasswordToken::class, 'getCredentials') ? new UsernamePasswordToken($user, null, 'main', $user->getRoles()) : new UsernamePasswordToken($user, 'main', $user->getRoles());
        $tokenStorage->setToken($token);

        $sessionId = 'test-sonata-user-bundle';

        $cookie = new Cookie('MOCKSESSID', $sessionId);
        $client->getCookieJar()->set($cookie);

        $mockSession = new MockFileSessionStorage($client->getKernel()->getCacheDir().'/sessions');
        $mockSession->setId($sessionId);
        $mockSession->start();
        $mockSession->setSessionData([
            '_sf2_attributes' => [
                '_security_user' => serialize($token),
            ],
        ]);
        $mockSession->save();

        return $token;
    }
}
