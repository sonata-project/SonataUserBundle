<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class RegistrationController
 *
 * @package Sonata\UserBundle\Controller
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class RegistrationController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function confirmedAction()
    {
        $response = parent::confirmedAction();

        if ($redirectRoute = $this->container->getParameter('sonata.user.register.confirm.redirect_route')) {
            return new RedirectResponse($this->container->get('router')->generate($redirectRoute, $this->container->getParameter('sonata.user.register.confirm.redirect_route_params')));
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function registerAction()
    {
        $response = parent::registerAction();

        if ($response instanceof RedirectResponse && ($url = $this->container->get('session')->get('sonata_user_redirect_url'))) {
            // Form has been submitted and we're redirected...
            return new RedirectResponse($url);
        }

        if (!isset($url)) {
            // Set URL
            $this->container->get('session')->set('sonata_user_redirect_url', $this->container->get('request')->headers->get('referer'));
        }

        return $response;
    }
}