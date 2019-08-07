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

namespace Sonata\UserBundle\EventListener;

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\UserBundle\GoogleAuthenticator\Helper;
use Sonata\UserBundle\Model\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Twig\Environment;

/**
 * Class TwoFactorLoginSuccessHandler is used for handling 2FA authorization for enabled roles and ips.
 *
 * @author Aleksej Krichevsky <krich.al.vl@gmail.com>
 */
final class TwoFactorLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var Environment
     */
    private $engine;

    /**
     * @var Helper
     */
    private $googleAuthenticator;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        Environment $engine,
        Helper $helper,
        UserManagerInterface $userManager,
        UrlGeneratorInterface $urlGenerator = null // NEXT_MAJOR: make it mandatory.
    ) {
        $this->engine = $engine;
        $this->googleAuthenticator = $helper;
        $this->userManager = $userManager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return RedirectResponse|Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        /** @var $user User */
        $user = $token->getUser();
        $needToHave2FA = $this->googleAuthenticator->needToHaveGoogle2FACode($request);

        if ($needToHave2FA && !$user->getTwoStepVerificationCode()) {
            $secret = $this->googleAuthenticator->generateSecret();
            $user->setTwoStepVerificationCode($secret);

            $qrCodeUrl = $this->googleAuthenticator->getUrl($user);
            $this->userManager->updateUser($user);

            return new Response($this->engine->render(
                '@SonataUser/Admin/Security/login.html.twig',
                [
                    'qrCodeUrl' => $qrCodeUrl,
                    'qrSecret' => $secret,
                    'base_template' => '@SonataAdmin/standard_layout.html.twig',
                    'error' => [],
                ]
            ));
        } elseif ($needToHave2FA && $user->getTwoStepVerificationCode()) {
            $request->getSession()->set($this->googleAuthenticator->getSessionKey($token), null);
        }

        // NEXT_MAJOR: remove hardcoded url.
        $url = $this->urlGenerator
            ? $this->urlGenerator->generate('sonata_admin_dashboard')
            : '/admin'
        ;

        return new RedirectResponse($url);
    }
}
