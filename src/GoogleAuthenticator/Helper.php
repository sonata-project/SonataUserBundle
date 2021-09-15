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
use Sonata\GoogleAuthenticator\GoogleQrUrl;
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
     * @var array<int, string>
     */
    private $forcedForRoles;

    /**
     * @var array<int, string>
     */
    private $trustedIpList;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var array<int, string> $trustedIpList IPs that will bypass 2FA authorization
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

    public function checkCode(UserInterface $user, string $code): bool
    {
        return $this->authenticator->checkCode($user->getTwoStepVerificationCode(), $code);
    }

    public function getUrl(UserInterface $user): string
    {
        return GoogleQrUrl::generate($user->getUsername(), $user->getTwoStepVerificationCode(), $this->server);
    }

    public function generateSecret(): string
    {
        return $this->authenticator->generateSecret();
    }

    public function getSessionKey(UsernamePasswordToken $token): string
    {
        return sprintf(
            'sonata_user_google_authenticator_%s_%s',
            $token->getFirewallName(),
            $token->getUserIdentifier()
        );
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
