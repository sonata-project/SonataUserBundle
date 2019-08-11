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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class CheckEmailAction
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Pool
     */
    private $adminPool;

    /**
     * @var TemplateRegistryInterface
     */
    private $templateRegistry;

    /**
     * @var int
     */
    private $resetTtl;

    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        Pool $adminPool,
        TemplateRegistryInterface $templateRegistry,
        int $resetTtl
    ) {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->adminPool = $adminPool;
        $this->templateRegistry = $templateRegistry;
        $this->resetTtl = $resetTtl;
    }

    public function __invoke(Request $request): Response
    {
        $username = $request->query->get('username');

        if (empty($username)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->urlGenerator->generate('sonata_user_admin_resetting_request'));
        }

        return new Response($this->twig->render('@SonataUser/Admin/Security/Resetting/checkEmail.html.twig', [
            'base_template' => $this->templateRegistry->getTemplate('layout'),
            'admin_pool' => $this->adminPool,
            'tokenLifetime' => ceil($this->resetTtl / 3600),
        ]));
    }
}
