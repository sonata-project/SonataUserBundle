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

namespace Sonata\UserBundle\Tests\Functional\Command;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Tests\App\AppKernel;
use Sonata\UserBundle\Tests\App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

class PromoteUserCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        static::bootKernel();

        $this->commandTester = new CommandTester(
            (new Application(static::createKernel()))->find('sonata:user:promote')
        );
    }

    public function testThrowsWhenMissingBothRoleAndSuperAdmin(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->commandTester->execute(['username' => 'sonata-user-test']);
    }

    public function testThrowsWhenPassingBothRoleAndSuperAdmin(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            'role' => 'CUSTOM_ROLE',
            'super-admin' => true,
        ]);
    }

    public function testThrowsIfUserNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            '--super-admin' => true,
        ]);
    }

    public function testDoesNothingIfRoleAlreadyAdded(): void
    {
        $user = $this->prepareData('sonata-user-test', false, ['ROLE_CUSTOM']);

        static::assertTrue($user->hasRole('ROLE_CUSTOM'));

        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            'role' => 'ROLE_CUSTOM',
        ]);

        $user = $this->refreshUser($user);

        static::assertTrue($user->hasRole('ROLE_CUSTOM'));
        static::assertStringContainsString('User "sonata-user-test" did already have "ROLE_CUSTOM" role.', $this->commandTester->getDisplay());
    }

    public function testAddCustomRole(): void
    {
        $user = $this->prepareData('sonata-user-test', false, []);

        static::assertFalse($user->hasRole('ROLE_CUSTOM'));

        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            'role' => 'ROLE_CUSTOM',
        ]);

        $user = $this->refreshUser($user);

        static::assertTrue($user->hasRole('ROLE_CUSTOM'));
        static::assertStringContainsString('Role "ROLE_CUSTOM" has been added to user "sonata-user-test".', $this->commandTester->getDisplay());
    }

    public function testBecomeSuperAdmin(): void
    {
        $user = $this->prepareData('sonata-user-test', false, []);

        static::assertFalse($user->isSuperAdmin());

        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            '--super-admin' => true,
        ]);

        $user = $this->refreshUser($user);

        static::assertTrue($user->isSuperAdmin());
        static::assertStringContainsString('User "sonata-user-test" has been promoted as a super administrator.', $this->commandTester->getDisplay());
    }

    /**
     * @return class-string<KernelInterface>
     */
    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    /**
     * @psalm-suppress UndefinedPropertyFetch
     *
     * @param string[] $roles
     */
    private function prepareData(string $username, bool $superAdmin, array $roles): UserInterface
    {
        // TODO: Simplify this when dropping support for Symfony 4.
        // @phpstan-ignore-next-line
        $container = method_exists(static::class, 'getContainer') ? static::getContainer() : static::$container;
        $manager = $container->get('doctrine.orm.entity_manager');
        \assert($manager instanceof EntityManagerInterface);

        $user = new User();
        $user->setUsername($username);
        $user->setEmail('email@localhost');
        $user->setPlainPassword('random_password');
        $user->setRoles($roles);
        $user->setSuperAdmin($superAdmin);
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
}
