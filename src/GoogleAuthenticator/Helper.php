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

namespace Sonata\UserBundle\GoogleAuthenticator;

use Google\Authenticator\GoogleAuthenticator as BaseGoogleAuthenticator;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Helper
{
    /**
     * @var string
     */
    protected $server;

    /**
     * @var BaseGoogleAuthenticator
     */
    protected $authenticator;

    /**
     * @var string[]
     */
    private $forcedForRoles;

    /**
     * @var string[]
     */
    private $trustedIpList;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param string[] $trustedIpList IPs that will bypass 2FA authorization
     */
    public function __construct(
        $server,
        BaseGoogleAuthenticator $authenticator,
        AuthorizationCheckerInterface $authorizationChecker,
        array $forcedForRoles = [],
        array $trustedIpList = []
    ) {
        $this->server = $server;
        $this->authenticator = $authenticator;
        $this->authorizationChecker = $authorizationChecker;
        $this->forcedForRoles = $forcedForRoles;
        $this->trustedIpList = $trustedIpList;
    }

    /**
     * @param $code
     *
     * @return bool
     */
    public function checkCode(UserInterface $user, $code)
    {
        return $this->authenticator->checkCode($user->getTwoStepVerificationCode(), $code);
    }

    /**
     * @return string
     */
    public function getUrl(UserInterface $user)
    {
        return $this->authenticator->getUrl($user->getUsername(), $this->server, $user->getTwoStepVerificationCode());
    }

    /**
     * @return string
     */
    public function generateSecret()
    {
        return $this->authenticator->generateSecret();
    }

    /**
     * @return string
     */
    public function getSessionKey(UsernamePasswordToken $token)
    {
        return sprintf('sonata_user_google_authenticator_%s_%s', $token->getProviderKey(), $token->getUsername());
    }

    public function needToHaveGoogle2FACode(Request $request): bool
    {
        if (\in_array($request->getClientIp(), $this->trustedIpList, true)) {
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
