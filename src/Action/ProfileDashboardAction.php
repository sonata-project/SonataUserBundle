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

use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

final class ProfileDashboardAction
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var array
     */
    private $profileBlocks;

    public function __construct(
        Environment $twig,
        TokenStorageInterface $tokenStorage,
        array $profileBlocks
    ) {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->profileBlocks = $profileBlocks;
    }

    public function __invoke(): Response
    {
        if (!$this->isAuthenticated()) {
            throw new \RuntimeException('You must add your user profile uri to security.access_control with role IS_AUTHENTICATED_FULLY.');
        }

        return new Response($this->twig->render('@SonataUser/Profile/dashboard.html.twig', [
            'blocks' => $this->profileBlocks,
        ]));
    }

    private function isAuthenticated(): bool
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return false;
        }

        $user = $token->getUser();

        return $user instanceof UserInterface;
    }
}
