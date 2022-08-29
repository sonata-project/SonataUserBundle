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
use Sonata\UserBundle\Tests\App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DemoteUserCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        static::bootKernel();

        $this->commandTester = new CommandTester(
            (new Application(static::createKernel()))->find('sonata:user:demote')
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

    public function testDoesNothingIfRoleIsNotOnUser(): void
    {
        $user = $this->prepareData('sonata-user-test', false, []);

        static::assertFalse($user->hasRole('ROLE_CUSTOM'));

        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            'role' => 'ROLE_CUSTOM',
        ]);

        $user = $this->refreshUser($user);

        static::assertFalse($user->hasRole('ROLE_CUSTOM'));
        static::assertStringContainsString('User "sonata-user-test" didn\'t have "ROLE_CUSTOM" role.', $this->commandTester->getDisplay());
    }

    public function testRemoveCustomRole(): void
    {
        $user = $this->prepareData('sonata-user-test', false, ['ROLE_CUSTOM']);

        static::assertTrue($user->hasRole('ROLE_CUSTOM'));

        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            'role' => 'ROLE_CUSTOM',
        ]);

        $user = $this->refreshUser($user);

        static::assertFalse($user->hasRole('ROLE_CUSTOM'));
        static::assertStringContainsString('Role "ROLE_CUSTOM" has been removed from user "sonata-user-test".', $this->commandTester->getDisplay());
    }

    public function testBecomeNormalUser(): void
    {
        $user = $this->prepareData('sonata-user-test', true, []);

        static::assertTrue($user->isSuperAdmin());

        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            '--super-admin' => true,
        ]);

        $user = $this->refreshUser($user);

        static::assertFalse($user->isSuperAdmin());
        static::assertStringContainsString('User "sonata-user-test" has been demoted as a simple user.', $this->commandTester->getDisplay());
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
