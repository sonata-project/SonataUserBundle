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

class ChangePasswordCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        static::bootKernel();

        $this->commandTester = new CommandTester(
            (new Application(static::createKernel()))->find('sonata:user:change-password')
        );
    }

    public function testThrowsWhenUserDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            'password' => 'password',
        ]);
    }

    public function testChangesUserPassword(): void
    {
        $user = $this->prepareData('sonata-user-test', 'old_password');

        static::assertSame($user->getPassword(), 'old_password');

        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            'password' => 'new_password',
        ]);

        $user = $this->refreshUser($user);

        static::assertSame($user->getPassword(), 'new_password');
        static::assertStringContainsString('Changed password for user "sonata-user-test".', $this->commandTester->getDisplay());
    }

    /**
     * @psalm-suppress UndefinedPropertyFetch
     */
    private function prepareData(string $username, string $password): UserInterface
    {
        // TODO: Simplify this when dropping support for Symfony 4.
        // @phpstan-ignore-next-line
        $container = method_exists(static::class, 'getContainer') ? static::getContainer() : static::$container;
        $manager = $container->get('doctrine.orm.entity_manager');
        \assert($manager instanceof EntityManagerInterface);

        $user = new User();
        $user->setUsername($username);
        $user->setEmail('email@localhost');
        $user->setPlainPassword($password);
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
}
