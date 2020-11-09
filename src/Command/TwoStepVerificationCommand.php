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

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\UserBundle\GoogleAuthenticator\Helper;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class TwoStepVerificationCommand extends Command
{
    protected static $defaultName = 'sonata:user:two-step-verification';

    /**
     * @var Helper|null
     */
    private $helper;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(
        ?string $name,
        ?Helper $helper,
        UserManagerInterface $userManager
    ) {
        parent::__construct($name);

        $this->helper = $helper;
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(): void
    {
        $this->addArgument(
            'username',
            InputArgument::REQUIRED,
            'The username to protect with a two step verification process'
        );
        $this->addOption('reset', null, InputOption::VALUE_NONE, 'Reset the current two step verification token');
        $this->setDescription(
            'Generate a two step verification process to secure an access (Ideal for super admin protection)'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (null === $this->helper) {
            throw new \RuntimeException('Two Step Verification process is not enabled');
        }

        $user = $this->userManager->findUserByUsernameOrEmail($input->getArgument('username'));

        if (false === $user instanceof UserInterface) {
            throw new \RuntimeException(sprintf('Unable to find the username : %s', $input->getArgument('username')));
        }

        if (!$user->getTwoStepVerificationCode() || $input->getOption('reset')) {
            $user->setTwoStepVerificationCode($this->helper->generateSecret());
            $this->userManager->updateUser($user);
        }

        $output->writeln([
            sprintf('<info>Username</info> : %s', $input->getArgument('username')),
            sprintf('<info>Secret</info> : %s', $user->getTwoStepVerificationCode()),
            sprintf('<info>Url</info> : %s', $this->helper->getUrl($user)),
        ]);

        return 0;
    }
}
