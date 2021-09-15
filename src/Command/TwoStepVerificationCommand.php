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

class TwoStepVerificationCommand extends Command
{
    /**
     * @var Helper
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

    public function configure(): void
    {
        $this->setName('sonata:user:two-step-verification');
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

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        if (null === $this->helper) {
            throw new \RuntimeException('Two Step Verification process is not enabled');
        }

        $user = $this->userManager->findUserByUsernameOrEmail($input->getArgument('username'));
        if (!$user) {
            throw new \RuntimeException(sprintf('Unable to find the username : %s', $input->getArgument('username')));
        }
        \assert($user instanceof UserInterface);

        if (!$user->getTwoStepVerificationCode() || $input->getOption('reset')) {
            $user->setTwoStepVerificationCode($this->helper->generateSecret());
            $this->userManager->updateUser($user);
        }

        $output->writeln([
            sprintf('<info>Username</info> : %s', $input->getArgument('username')),
            sprintf('<info>Secret</info> : %s', $user->getTwoStepVerificationCode()),
            sprintf('<info>Url</info> : %s', $this->helper->getUrl($user)),
        ]);
    }
}
