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

namespace Sonata\UserBundle\Action;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SendEmailAction
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Pool
     */
    private $adminPool;

    /**
     * @var TemplateRegistryInterface
     */
    private $templateRegistry;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var int
     */
    private $resetTtl;

    public function __construct(
        EngineInterface $templating,
        UrlGeneratorInterface $urlGenerator,
        Pool $adminPool,
        TemplateRegistryInterface $templateRegistry,
        UserManagerInterface $userManager,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator,
        int $resetTtl
    ) {
        $this->templating = $templating;
        $this->urlGenerator = $urlGenerator;
        $this->adminPool = $adminPool;
        $this->templateRegistry = $templateRegistry;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->resetTtl = $resetTtl;
    }

    public function __invoke(Request $request): Response
    {
        $username = $request->request->get('username');

        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return $this->templating->renderResponse('@SonataUser/Admin/Security/Resetting/request.html.twig', [
                'base_template' => $this->templateRegistry->getTemplate('layout'),
                'admin_pool' => $this->adminPool,
                'invalid_username' => $username,
            ]);
        }

        if (null !== $user && !$user->isPasswordRequestNonExpired($this->resetTtl)) {
            if (!$user->isAccountNonLocked()) {
                return new RedirectResponse($this->urlGenerator->generate('sonata_user_admin_resetting_request'));
            }

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $this->mailer->sendResettingEmailMessage($user);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->userManager->updateUser($user);
        }

        return new RedirectResponse($this->urlGenerator->generate('sonata_user_admin_resetting_check_email', [
            'username' => $username,
        ]));
    }
}
