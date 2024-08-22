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
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class CheckEmailAction
{
    public function __construct(
        private Environment $twig,
        private Pool $adminPool,
        private TemplateRegistryInterface $templateRegistry,
        private int $tokenTtl
    ) {
    }

    public function __invoke(): Response
    {
        return new Response($this->twig->render('@SonataUser/Admin/Security/Resetting/checkEmail.html.twig', [
            'base_template' => $this->templateRegistry->getTemplate('layout'),
            'admin_pool' => $this->adminPool,
            'tokenLifetime' => ceil($this->tokenTtl / 3600),
        ]));
    }
}
