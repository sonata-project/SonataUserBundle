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
     * @dataProvider provideFormsUrlsCases
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
    public static function provideFormsUrlsCases(): iterable
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

        $user = $this->prepareData();

        static::assertSame('random_password', $user->getPassword());

        $client->request('GET', '/admin/tests/app/user/1/edit', [
            'uniqid' => 'user',
        ]);
        $client->submitForm('btn_update_and_list', [
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

    private function prepareData(): UserInterface
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
        $manager->clear();

        return $user;
    }

    private function refreshUser(UserInterface $user): UserInterface
    {
        $manager = static::getContainer()->get('doctrine.orm.entity_manager');
        \assert($manager instanceof EntityManagerInterface);

        $user = $manager->find(User::class, $user->getId());
        \assert(null !== $user);

        return $user;
    }

    private function loginUser(UserInterface $user, KernelBrowser $client): TokenInterface
    {
        $tokenStorage = static::getContainer()->get('security.token_storage');
        \assert($tokenStorage instanceof TokenStorageInterface);

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
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
