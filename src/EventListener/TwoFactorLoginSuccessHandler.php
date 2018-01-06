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

use Sonata\UserBundle\GoogleAuthenticator\Helper;
use Sonata\UserBundle\Model\User;
use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Class TwoFactorLoginSuccessHandler is used for handling 2FA authorization for enabled roles and ips.
 *
 * @author Aleksej Krichevsky <krich.al.vl@gmail.com>
 */
final class TwoFactorLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var EngineInterface
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
     * @var array
     */
    private $forcedForRoles;

    /**
     * @var array
     */
    private $ipWhiteList;

    /**
     * @param array $forcedForRole Roles that need to force use 2FA authorization
     * @param array $ipWhiteList   IP's that will skip 2FA authorization
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        EngineInterface $engine,
        Helper $helper,
        UserManagerInterface $userManager,
        array $forcedForRole,
        array $ipWhiteList
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->engine = $engine;
        $this->googleAuthenticator = $helper;
        $this->userManager = $userManager;
        $this->forcedForRoles = $forcedForRole;
        $this->ipWhiteList = $ipWhiteList;
    }

    /**
     * @return RedirectResponse|Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        /** @var $user User */
        $user = $token->getUser();
        $redirectResponse = new RedirectResponse('/admin');

        $needToHave2FA = $this->needToHaveGoogle2FACode($request);

        if ($needToHave2FA && !$user->getTwoStepVerificationCode()) {
            $secret = $this->googleAuthenticator->generateSecret();
            $user->setTwoStepVerificationCode($secret);

            $qrCodeUrl = $this->googleAuthenticator->getUrl($user);
            $this->userManager->updateUser($user);

            return $this->engine->renderResponse(
                '@SonataUser/Admin/Security/login.html.twig',
                [
                    'qrCodeUrl' => $qrCodeUrl,
                    'qrSecret' => $secret,
                    'base_template' => '@SonataAdmin/standard_layout.html.twig',
                    'error' => [],
                ]
            );
        } elseif ($needToHave2FA && $user->getTwoStepVerificationCode()) {
            $request->getSession()->set($this->googleAuthenticator->getSessionKey($token), null);
        }

        return $redirectResponse;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function needToHaveGoogle2FACode(Request $request): bool
    {
        $ip = $request->server->get('HTTP_X_FORWARDED_FOR', $request->server->get('REMOTE_ADDR'));
        if (in_array($ip, $this->ipWhiteList)) {
            return false;
        }

        foreach ($this->forcedForRoles as $role) {
            if ($this->authorizationChecker->isGranted($role)) {
                return true;
            }
        }

        return false;
    }
}
