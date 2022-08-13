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

namespace Sonata\UserBundle\Command;

use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
#[AsCommand(name: 'sonata:user:create', description: 'Create a user')]
final class CreateUserCommand extends Command
{
    // TODO: Remove static properties when support for Symfony < 5.4 is dropped.
    protected static $defaultName = 'sonata:user:create';
    protected static $defaultDescription = 'Create a user';

    private UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        parent::__construct();

        $this->userManager = $userManager;
    }

    protected function configure(): void
    {
        \assert(null !== static::$defaultDescription);

        $this
            // TODO: Remove setDescription when support for Symfony < 5.4 is dropped.
            ->setDescription(static::$defaultDescription)
            ->setDefinition([
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ])
            ->setHelp(
                <<<'EOT'
                    The <info>%command.full_name%</info> command creates a user:

                      <info>php %command.full_name% matthieu matthieu@example.com mypassword</info>

                    You can create a super admin via the super-admin flag:

                      <info>php %command.full_name% admin admi@example.com mypassword --super-admin</info>

                    You can create an inactive user (will not be able to log in):

                      <info>php %command.full_name% user user@example.com mypassword --inactive</info>

                    EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $inactive = $input->getOption('inactive');
        $superAdmin = $input->getOption('super-admin');

        $user = $this->userManager->create();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled(!$inactive);
        $user->setSuperAdmin($superAdmin);

        $this->userManager->save($user);

        $output->writeln(sprintf('Created user "%s".', $username));

        return 0;
    }
}
