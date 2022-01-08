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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @final since sonata-project/user-bundle 4.15
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class GlobalVariables
{
    protected ContainerInterface $container;

    /**
     * @psalm-suppress ContainerDependency
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getImpersonating(): string
    {
        $impersonating = $this->container->getParameter('sonata.user.impersonating');
        \assert(\is_string($impersonating));

        return $impersonating;
    }

    public function getDefaultAvatar(): string
    {
        $defaultAvatar = $this->container->getParameter('sonata.user.default_avatar');
        \assert(\is_string($defaultAvatar));

        return $defaultAvatar;
    }

    /**
     * @return AdminInterface<object>
     */
    public function getUserAdmin(): AdminInterface
    {
        $userAdmin = $this->container->get('sonata.user.admin.user');
        \assert($userAdmin instanceof AdminInterface);

        return $userAdmin;
    }
}
