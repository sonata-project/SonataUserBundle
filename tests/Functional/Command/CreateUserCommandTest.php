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

use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        static::bootKernel();

        $this->commandTester = new CommandTester(
            (new Application(static::createKernel()))->find('sonata:user:create')
        );
    }

    public function testCreatesAnActiveUser(): void
    {
        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            'email' => 'email@localhost',
            'password' => 'password',
        ]);

        $createdUser = $this->find('sonata-user-test');

        static::assertSame('sonata-user-test', $createdUser->getUserIdentifier());
        static::assertSame('email@localhost', $createdUser->getEmail());
        static::assertSame('password', $createdUser->getPassword());
        static::assertTrue($createdUser->isEnabled());
        static::assertNotNull($createdUser->getCreatedAt());
    }

    public function testCreatesAnInactiveUser(): void
    {
        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            'email' => 'email@localhost',
            'password' => 'password',
            '--inactive' => true,
        ]);

        $createdUser = $this->find('sonata-user-test');

        static::assertFalse($createdUser->isEnabled());
    }

    public function testCreatesAnSuperAdminUser(): void
    {
        $this->commandTester->execute([
            'username' => 'sonata-user-test',
            'email' => 'email@localhost',
            'password' => 'password',
            '--super-admin' => true,
        ]);

        $createdUser = $this->find('sonata-user-test');

        static::assertTrue($createdUser->isSuperAdmin());
    }

    /**
     * @psalm-suppress UndefinedPropertyFetch
     */
    private function find(string $username): UserInterface
    {
        // TODO: Simplify this when dropping support for Symfony 4.
        // @phpstan-ignore-next-line
        $container = method_exists(static::class, 'getContainer') ? static::getContainer() : static::$container;
        $manager = $container->get('sonata.user.manager.user');
        \assert($manager instanceof UserManagerInterface);

        $user = $manager->findUserByUsername($username);
        \assert(null !== $user);

        return $user;
    }
}
