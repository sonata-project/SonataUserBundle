<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * GlobalVariables
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class GlobalVariables
{
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getImpersonatingRoute()
    {
        return $this->container->getParameter('sonata.user.impersonating_route');
    }
}