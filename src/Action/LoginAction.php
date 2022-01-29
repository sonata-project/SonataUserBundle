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

use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class LoginAction
{
    private Environment $twig;

    private UrlGeneratorInterface $urlGenerator;

    private AuthenticationUtils $authenticationUtils;

    private Pool $adminPool;

    private TemplateRegistryInterface $templateRegistry;

    private TokenStorageInterface $tokenStorage;

    private TranslatorInterface $translator;

    private ?CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        AuthenticationUtils $authenticationUtils,
        Pool $adminPool,
        TemplateRegistryInterface $templateRegistry,
        TokenStorageInterface $tokenStorage,
        TranslatorInterface $translator,
        ?CsrfTokenManagerInterface $csrfTokenManager = null
    ) {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->authenticationUtils = $authenticationUtils;
        $this->adminPool = $adminPool;
        $this->templateRegistry = $templateRegistry;
        $this->tokenStorage = $tokenStorage;
        $this->translator = $translator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function __invoke(Request $request): Response
    {
        if ($this->isAuthenticated()) {
            $request->getSession()->getFlashBag()->add(
                'sonata_user_error',
                $this->translator->trans('sonata_user_already_authenticated', [], 'SonataUserBundle')
            );

            return new RedirectResponse($this->urlGenerator->generate('sonata_admin_dashboard'));
        }

        $csrfToken = null;
        if (null !== $this->csrfTokenManager) {
            $csrfToken = $this->csrfTokenManager->getToken('authenticate')->getValue();
        }

        return new Response($this->twig->render('@SonataUser/Admin/Security/login.html.twig', [
            'admin_pool' => $this->adminPool,
            'base_template' => $this->templateRegistry->getTemplate('layout'),
            'csrf_token' => $csrfToken,
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'reset_route' => $this->urlGenerator->generate('sonata_user_admin_resetting_request'),
        ]));
    }

    private function isAuthenticated(): bool
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            return false;
        }

        $user = $token->getUser();

        return $user instanceof UserInterface;
    }
}
