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

use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Security\LoginManagerInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

final class ResetAction
{
    use LoggerAwareTrait;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var Pool
     */
    private $adminPool;

    /**
     * @var TemplateRegistryInterface
     */
    private $templateRegistry;

    /**
     * @var FactoryInterface
     */
    private $formFactory;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var LoginManagerInterface
     */
    private $loginManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var int
     */
    private $resetTtl;

    /**
     * @var string
     */
    private $firewallName;

    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        AuthorizationCheckerInterface $authorizationChecker,
        Pool $adminPool,
        TemplateRegistryInterface $templateRegistry,
        FactoryInterface $formFactory,
        UserManagerInterface $userManager,
        LoginManagerInterface $loginManager,
        TranslatorInterface $translator,
        SessionInterface $session,
        int $resetTtl,
        string $firewallName
    ) {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->authorizationChecker = $authorizationChecker;
        $this->adminPool = $adminPool;
        $this->templateRegistry = $templateRegistry;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->loginManager = $loginManager;
        $this->translator = $translator;
        $this->session = $session;
        $this->resetTtl = $resetTtl;
        $this->firewallName = $firewallName;
        $this->logger = new NullLogger();
    }

    public function __invoke(Request $request, $token): Response
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->urlGenerator->generate('sonata_admin_dashboard'));
        }

        $user = $this->userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (!$user->isPasswordRequestNonExpired($this->resetTtl)) {
            return new RedirectResponse($this->urlGenerator->generate('sonata_user_admin_resetting_request'));
        }

        $form = $this->formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setEnabled(true);

            $message = $this->translator->trans('resetting.flash.success', [], 'FOSUserBundle');
            $this->session->getFlashBag()->add('success', $message);

            $response = new RedirectResponse($this->urlGenerator->generate('sonata_admin_dashboard'));

            try {
                $this->loginManager->logInUser($this->firewallName, $user, $response);
                $user->setLastLogin(new \DateTime());
            } catch (AccountStatusException $ex) {
                // We simply do not authenticate users which do not pass the user
                // checker (not enabled, expired, etc.).
                $this->logger->warning(sprintf(
                    'Unable to login user %d after password reset',
                    $user->getId()
                ), ['exception' => $ex]);
            }

            $this->userManager->updateUser($user);

            return $response;
        }

        return new Response($this->twig->render('@SonataUser/Admin/Security/Resetting/reset.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
            'base_template' => $this->templateRegistry->getTemplate('layout'),
            'admin_pool' => $this->adminPool,
        ]));
    }
}
