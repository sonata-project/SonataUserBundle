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
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Translation\TranslatorInterface;

final class ResetAction extends Controller
{
    use LoggerAwareTrait;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Pool
     */
    protected $adminPool;

    /**
     * @var TemplateRegistryInterface
     */
    protected $templateRegistry;

    /**
     * @var FactoryInterface
     */
    protected $formFactory;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var LoginManagerInterface
     */
    protected $loginManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var int
     */
    protected $resetTtl;

    /**
     * @var string
     */
    protected $firewallName;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        RouterInterface $router,
        Pool $adminPool,
        TemplateRegistryInterface $templateRegistry,
        FactoryInterface $formFactory,
        UserManagerInterface $userManager,
        LoginManagerInterface $loginManager,
        TranslatorInterface $translator,
        int $resetTtl,
        string $firewallName
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->router = $router;
        $this->adminPool = $adminPool;
        $this->templateRegistry = $templateRegistry;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->loginManager = $loginManager;
        $this->translator = $translator;
        $this->resetTtl = $resetTtl;
        $this->firewallName = $firewallName;
    }

    public function __invoke(Request $request, $token)
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->router->generate('sonata_admin_dashboard'));
        }

        $user = $this->userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (!$user->isPasswordRequestNonExpired($this->resetTtl)) {
            return new RedirectResponse($this->generateUrl('sonata_user_admin_resetting_request'));
        }

        $form = $this->formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setEnabled(true);

            $message = $this->translator->trans('resetting.flash.success', [], 'FOSUserBundle');
            $this->addFlash('success', $message);

            $response = new RedirectResponse($this->generateUrl('sonata_admin_dashboard'));

            try {
                $this->loginManager->logInUser($this->firewallName, $user, $response);
                $user->setLastLogin(new \DateTime());
            } catch (AccountStatusException $ex) {
                // We simply do not authenticate users which do not pass the user
                // checker (not enabled, expired, etc.).
                if ($this->logger) {
                    $this->getLogger()->warning(sprintf(
                        'Unable to login user %d after password reset',
                        $user->getId()
                    );
                }
            }

            $this->userManager->updateUser($user);

            return $response;
        }

        return $this->render('@SonataUser/Admin/Security/Resetting/reset.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
            'base_template' => $this->templateRegistry->getTemplate('layout'),
            'admin_pool' => $this->adminPool,
        ]);
    }
}