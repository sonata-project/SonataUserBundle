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
#[AsCommand(name: 'sonata:user:demote', description: 'Demotes a user by removing a role')]
final class DemoteUserCommand extends Command
{
    // TODO: Remove static properties when support for Symfony < 5.4 is dropped.
    protected static $defaultName = 'sonata:user:demote';
    protected static $defaultDescription = 'Demotes a user by removing a role';

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
                new InputArgument('role', InputArgument::OPTIONAL, 'The role'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Instead specifying role, use this to quickly add the super administrator role'),
            ])
            ->setHelp(
                <<<'EOT'
                    The <info>%command.full_name%</info> command demotes a user by removing a role

                      <info>php %command.full_name% matthieu ROLE_CUSTOM</info>
                      <info>php %command.full_name% --super-admin matthieu</info>
                    EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $role = $input->getArgument('role');
        $superAdmin = (true === $input->getOption('super-admin'));

        if (null !== $role && $superAdmin) {
            throw new \InvalidArgumentException('You can pass either the role or the --super-admin option (but not both simultaneously).');
        }

        if (null === $role && !$superAdmin) {
            throw new \InvalidArgumentException('Not enough arguments.');
        }

        $user = $this->userManager->findUserByUsername($username);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }

        if ($superAdmin) {
            $user->setSuperAdmin(false);

            $output->writeln(sprintf('User "%s" has been demoted as a simple user. This change will not apply until the user logs out and back in again.', $username));
        } elseif ($user->hasRole($role)) {
            $user->removeRole($role);

            $output->writeln(sprintf('Role "%s" has been removed from user "%s". This change will not apply until the user logs out and back in again.', $role, $username));
        } else {
            $output->writeln(sprintf('User "%s" didn\'t have "%s" role.', $username, $role));
        }

        $this->userManager->save($user);

        return 0;
    }
}
