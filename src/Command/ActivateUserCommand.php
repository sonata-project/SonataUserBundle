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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @internal
 */
final class ActivateUserCommand extends Command
{
    protected static $defaultName = 'sonata:user:activate';
    protected static $defaultDescription = 'Activate a user';

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
                new InputArgument('username', InputArgument::OPTIONAL, 'The username'),
            ])
            ->setHelp(
                <<<'EOT'
The <info>%command.full_name%</info> command activates a user (so they will be able to log in):

  <info>php %command.full_name% matthieu</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        \assert(null !== $username);

        $user = $this->userManager->findUserByUsername($username);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }

        $user->setEnabled(true);

        $this->userManager->save($user);

        $output->writeln(sprintf('User "%s" has been activated.', $username));

        return 0;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $username = $input->getArgument('username');

        if (null === $username || '' === $username) {
            $question = new Question('Please choose a username: ');
            $question->setValidator(static function (?string $username) {
                if (null === $username) {
                    throw new \InvalidArgumentException('Username can not be empty');
                }

                return $username;
            });
            $answer = $this->getHelper('question')->ask($input, $output, $question);

            $input->setArgument('username', $answer);
        }
    }
}
