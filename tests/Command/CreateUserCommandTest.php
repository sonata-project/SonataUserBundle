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

namespace Sonata\UserBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Command\CreateUserCommand;
use Sonata\UserBundle\Util\UserManipulator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends TestCase
{
    public function testExecute()
    {
        $commandTester = $this->createCommandTester($this->getManipulator('user', 'pass', 'email', true, false));
        $exitCode = $commandTester->execute([
            'username' => 'user',
            'email' => 'email',
            'password' => 'pass',
        ], [
            'decorated' => false,
            'interactive' => false,
        ]);

        $this->assertSame(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Created user user/', $commandTester->getDisplay());
    }

    public function testExecuteInteractiveWithQuestionHelper()
    {
        $application = new Application();

        $helper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->setMethods(['ask'])
            ->getMock();

        $helper->expects($this->at(0))
            ->method('ask')
            ->willReturn('user');

        $helper->expects($this->at(1))
            ->method('ask')
            ->willReturn('email');

        $helper->expects($this->at(2))
            ->method('ask')
            ->willReturn('pass');

        $application->getHelperSet()->set($helper, 'question');

        $commandTester = $this->createCommandTester(
            $this->getManipulator('user', 'pass', 'email', true, false),
            $application
        );
        $exitCode = $commandTester->execute([], [
            'decorated' => false,
            'interactive' => true,
        ]);

        $this->assertSame(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Created user user/', $commandTester->getDisplay());
    }

    /**
     * @return CommandTester
     */
    private function createCommandTester(UserManipulator $manipulator, ?Application $application = null)
    {
        if (null === $application) {
            $application = new Application();
        }

        $application->setAutoExit(false);

        $command = new CreateUserCommand($manipulator);

        $application->add($command);

        return new CommandTester($application->find('fos:user:create'));
    }

    /**
     * @param $username
     * @param $password
     * @param $email
     * @param $active
     * @param $superadmin
     *
     * @return mixed
     */
    private function getManipulator($username, $password, $email, $active, $superadmin)
    {
        $manipulator = $this->getMockBuilder('FOS\UserBundle\Util\UserManipulator')
            ->disableOriginalConstructor()
            ->getMock();

        $manipulator
            ->expects($this->once())
            ->method('create')
            ->with($username, $password, $email, $active, $superadmin)
        ;

        return $manipulator;
    }
}
