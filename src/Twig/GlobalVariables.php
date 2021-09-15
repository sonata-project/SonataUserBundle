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
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class GlobalVariables
{
    protected $container;

    /**
     * @psalm-suppress ContainerDependency
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getImpersonating(): ?string
    {
        return $this->container->getParameter('sonata.user.impersonating');
    }

    public function getDefaultAvatar(): ?string
    {
        return $this->container->getParameter('sonata.user.default_avatar');
    }

    /**
     * @return AdminInterface|object
     */
    public function getUserAdmin(): ?AdminInterface
    {
        return $this->container->get('sonata.user.admin.user');
    }
}
