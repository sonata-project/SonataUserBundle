<?php

namespace Sonata\UserBundle\Action\PasswordResetting;

use App\Form\ResetPasswordRequestFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;

class RequestAction extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer
            );
        }

        return $this->render('security/reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }
}
