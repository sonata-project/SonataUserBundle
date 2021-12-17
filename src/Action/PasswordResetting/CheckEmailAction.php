<?php

namespace Sonata\UserBundle\Action\PasswordResetting;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;

class CheckEmailAction extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __invoke(Request $request): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly .
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('security/reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }
}
