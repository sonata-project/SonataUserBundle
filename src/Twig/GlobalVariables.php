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

namespace Sonata\UserBundle\Twig;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class GlobalVariables
{
    private ?Pool $pool;

    private string $defaultAvatar;

    private bool $impersonatingEnabled;

    private string $impersonatingRoute;

    /**
     * @var array<string, mixed>
     */
    private array $impersonatingRouteParameters;

    /**
     * @param array<string, mixed> $impersonatingRouteParameters
     */
    public function __construct(
        ?Pool $pool,
        string $defaultAvatar,
        bool $impersonatingEnabled,
        string $impersonatingRoute,
        array $impersonatingRouteParameters = []
    ) {
        $this->pool = $pool;
        $this->defaultAvatar = $defaultAvatar;
        $this->impersonatingEnabled = $impersonatingEnabled;
        $this->impersonatingRoute = $impersonatingRoute;
        $this->impersonatingRouteParameters = $impersonatingRouteParameters;
    }

    /**
     * @return AdminInterface<object>
     */
    public function getUserAdmin(): AdminInterface
    {
        if (null === $this->pool) {
            throw new \LogicException('Unable to get the UserAdmin, admin pool is not configured. You should install SonataAdminBundle in order to use admin-related features.');
        }

        return $this->pool->getAdminByAdminCode('sonata.user.admin.user');
    }

    public function getDefaultAvatar(): string
    {
        return $this->defaultAvatar;
    }

    public function isImpersonatingEnabled(): bool
    {
        return $this->impersonatingEnabled;
    }

    public function getImpersonatingRoute(): string
    {
        return $this->impersonatingRoute;
    }

    /**
     * @return array<string, mixed>
     */
    public function getImpersonatingRouteParameters(): array
    {
        return $this->impersonatingRouteParameters;
    }
}
