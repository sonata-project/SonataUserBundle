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
use Sonata\UserBundle\Form\Type\ResettingFormType;
use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ResetAction
{
    public function __construct(
        private Environment $twig,
        private UrlGeneratorInterface $urlGenerator,
        private AuthorizationCheckerInterface $authorizationChecker,
        private Pool $adminPool,
        private TemplateRegistryInterface $templateRegistry,
        private FormFactoryInterface $formFactory,
        private UserManagerInterface $userManager,
        private TranslatorInterface $translator,
        private int $tokenTtl
    ) {
    }

    public function __invoke(Request $request, string $token): Response
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->urlGenerator->generate('sonata_admin_dashboard'));
        }

        $user = $this->userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (!$user->isPasswordRequestNonExpired($this->tokenTtl)) {
            return new RedirectResponse($this->urlGenerator->generate('sonata_user_admin_resetting_request'));
        }

        $form = $this->formFactory->create(ResettingFormType::class);
        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setEnabled(true);

            /**
             * TODO: Use instanceof FlashBagAwareSessionInterface when dropping Symfony 5 support.
             *
             * @psalm-suppress UndefinedInterfaceMethod
             * @phpstan-ignore-next-line
             */
            $request->getSession()->getFlashBag()->add(
                'success',
                $this->translator->trans('resetting.flash.success', [], 'SonataUserBundle')
            );

            $response = new RedirectResponse($this->urlGenerator->generate('sonata_admin_dashboard'));

            $this->userManager->save($user);

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
