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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class RequestAction extends Controller
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Pool
     */
    protected $adminPool;

    /**
     * @var TemplateRegistryInterface
     */
    protected $templateRegistry;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, RouterInterface $router, Pool $adminPool, TemplateRegistryInterface $templateRegistry)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->router = $router;
        $this->adminPool = $adminPool;
        $this->templateRegistry = $templateRegistry;
    }

    public function __invoke(Request $request)
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->router->generate('sonata_admin_dashboard'));
        }

        return $this->render('@SonataUser/Admin/Security/Resetting/request.html.twig', [
            'base_template' => $this->templateRegistry->getTemplate('layout'),
            'admin_pool' => $this->adminPool,
        ]);
    }
}
