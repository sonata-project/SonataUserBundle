<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Controller;

use FOS\UserBundle\Controller\ResettingController;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class AdminResettingController extends ResettingController
{
    /**
     * {@inheritdoc}
     */
    public function requestAction()
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->generateUrl('sonata_admin_dashboard'));
        }

        return $this->render('SonataUserBundle:Admin:Security/Resetting/request.html.twig', array(
            'base_template' => $this->get('sonata.admin.pool')->getTemplate('layout'),
            'admin_pool'    => $this->get('sonata.admin.pool'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');

        /** @var $user UserInterface */
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return $this->render('SonataUserBundle:Admin:Security/Resetting/request.html.twig', array(
                'invalid_username' => $username,
                'base_template'    => $this->get('sonata.admin.pool')->getTemplate('layout'),
                'admin_pool'       => $this->get('sonata.admin.pool'),
            ));
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return $this->render('SonataUserBundle:Admin:Security/Resetting/passwordAlreadyRequested.html.twig', array(
                'base_template'    => $this->get('sonata.admin.pool')->getTemplate('layout'),
                'admin_pool'       => $this->get('sonata.admin.pool'),
            ));
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);

        return new RedirectResponse($this->generateUrl('sonata_user_admin_resetting_check_email',
            array('email' => $this->getObfuscatedEmail($user))
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->query->get('email');

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('sonata_user_admin_resetting_request'));
        }

        return $this->render('SonataUserBundle:Admin:Security/Resetting/checkEmail.html.twig', array(
            'email'         => $email,
            'base_template' => $this->get('sonata.admin.pool')->getTemplate('layout'),
            'admin_pool'    => $this->get('sonata.admin.pool'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function resetAction(Request $request, $token)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->generateUrl('sonata_admin_dashboard'));
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.resetting.form.factory');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return new RedirectResponse($this->generateUrl('sonata_user_admin_resetting_request'));
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $this->setFlash('sonata_user_success', 'resetting.flash.success');
                $url = $this->generateUrl('sonata_admin_dashboard');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('SonataUserBundle:Admin:Security/Resetting/reset.html.twig', array(
            'token'         => $token,
            'form'          => $form->createView(),
            'base_template' => $this->container->get('sonata.admin.pool')->getTemplate('layout'),
            'admin_pool'    => $this->container->get('sonata.admin.pool'),
        ));
    }
}
