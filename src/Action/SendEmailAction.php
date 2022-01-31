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

use Sonata\UserBundle\Form\Type\ResetPasswordRequestFormType;
use Sonata\UserBundle\Mailer\MailerInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use Sonata\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SendEmailAction
{
    private UrlGeneratorInterface $urlGenerator;

    private UserManagerInterface $userManager;

    private MailerInterface $mailer;

    private TokenGeneratorInterface $tokenGenerator;

    private FormFactoryInterface $formFactory;

    private int $retryTtl;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        UserManagerInterface $userManager,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator,
        FormFactoryInterface $formFactory,
        int $retryTtl
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->formFactory = $formFactory;
        $this->retryTtl = $retryTtl;
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->get('username')->getData();

            $user = $this->userManager->findUserByUsernameOrEmail($username);

            if (null !== $user && $user->isEnabled() && !$user->isPasswordRequestNonExpired($this->retryTtl) && $user->isAccountNonLocked()) {
                if (null === $user->getConfirmationToken()) {
                    $user->setConfirmationToken($this->tokenGenerator->generateToken());
                }

                $this->mailer->sendResettingEmailMessage($user);
                $user->setPasswordRequestedAt(new \DateTime());
                $this->userManager->save($user);
            }
        }

        return new RedirectResponse($this->urlGenerator->generate('sonata_user_admin_resetting_check_email', [
            'username' => $username,
        ]));
    }
}
