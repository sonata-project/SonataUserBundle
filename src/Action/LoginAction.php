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
    public function __construct(
        private Environment $twig,
        private UrlGeneratorInterface $urlGenerator,
        private AuthenticationUtils $authenticationUtils,
        private Pool $adminPool,
        private TemplateRegistryInterface $templateRegistry,
        private TokenStorageInterface $tokenStorage,
        private TranslatorInterface $translator,
        private ?CsrfTokenManagerInterface $csrfTokenManager = null,
        private bool $resetMail = true
    ) {
    }

    public function __invoke(Request $request): Response
    {
        if ($this->isAuthenticated()) {
            /**
             * TODO: Use instanceof FlashBagAwareSessionInterface when dropping Symfony 5 support.
             *
             * @psalm-suppress UndefinedInterfaceMethod
             * @phpstan-ignore-next-line
             */
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
            'reset_route' => $this->resetMail ? $this->urlGenerator->generate('sonata_user_admin_resetting_request') : null,
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
