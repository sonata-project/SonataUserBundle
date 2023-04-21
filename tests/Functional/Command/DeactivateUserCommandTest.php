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

class DeactivateUserCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        static::bootKernel();

        $this->commandTester = new CommandTester(
            (new Application(static::createKernel()))->find('sonata:user:deactivate')
        );
    }

    public function testThrowsWhenUserDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->commandTester->execute(['username' => 'sonata-user-test']);
    }

    public function testDoesNothingToAnAlreadyDeactiveUser(): void
    {
        $user = $this->prepareData('sonata-user-test', false);

        $this->commandTester->execute(['username' => 'sonata-user-test']);

        $user = $this->refreshUser($user);

        static::assertFalse($user->isEnabled());
    }

    public function testDeactivatesUser(): void
    {
        $user = $this->prepareData('sonata-user-test', true);

        $this->commandTester->execute(['username' => 'sonata-user-test']);

        $user = $this->refreshUser($user);

        static::assertFalse($user->isEnabled());
        static::assertStringContainsString('User "sonata-user-test" has been activated.', $this->commandTester->getDisplay());
    }

    private function prepareData(string $username, bool $enabled): UserInterface
    {
        $manager = static::getContainer()->get('doctrine.orm.entity_manager');
        \assert($manager instanceof EntityManagerInterface);

        $user = new User();
        $user->setUsername($username);
        $user->setEmail('email@localhost');
        $user->setPlainPassword('random_password');
        $user->setSuperAdmin(true);
        $user->setEnabled($enabled);

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
}
