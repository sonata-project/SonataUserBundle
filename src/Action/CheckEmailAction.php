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

final class CheckEmailAction extends Controller
{
    /**
     * @var Pool
     */
    protected $adminPool;

    /**
     * @var TemplateRegistryInterface
     */
    protected $templateRegistry;

    /**
     * @var int
     */
    protected $resetTtl;

    public function __construct(
        Pool $adminPool,
        TemplateRegistryInterface $templateRegistry,
        int $resetTtl
    ) {
        $this->adminPool = $adminPool;
        $this->templateRegistry = $templateRegistry;
        $this->resetTtl = $resetTtl;
    }

    public function __invoke(Request $request)
    {
        $username = $request->query->get('username');

        if (empty($username)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('sonata_user_admin_resetting_request'));
        }

        return $this->render('@SonataUser/Admin/Security/Resetting/checkEmail.html.twig', [
            'base_template' => $this->templateRegistry->getTemplate('layout'),
            'admin_pool' => $this->adminPool,
            'tokenLifetime' => ceil($this->resetTtl / 3600),
        ]);
    }
}
