<?php

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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @internal
 */
final class ChangePasswordCommand extends Command
{
    protected static $defaultName = 'sonata:user:change-password';
    protected static $defaultDescription = 'Change the password of a user';

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
            ->setDescription(static::$defaultDescription)
            ->setDefinition([
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
            ])
            ->setHelp(
                <<<'EOT'
The <info>%command.full_name%</info> command changes the password of a user:

  <info>php %command.full_name% matthieu mypassword</info>

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $user = $this->userManager->findUserByUsername($username);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }

        $user->setPlainPassword($password);

        $this->userManager->save($user);

        $output->writeln(sprintf('Changed password for user <comment>%s</comment>', $username));

        return 0;
    }
}
