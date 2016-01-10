<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminSecurityController extends SecurityController
{
    /**
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function loginAction(Request $request)
    {
        if ($this->getUser() instanceof UserInterface) {
            $this->get('session')->getFlashBag()->set('sonata_user_error', 'sonata_user_already_authenticated');
            $url = $this->generateUrl('sonata_admin_dashboard');

            return $this->redirect($url);
        }

        $response = parent::loginAction($request);

        if ($this->isGranted('ROLE_ADMIN')) {
            $refererUri = $request->server->get('HTTP_REFERER');

            return $this->redirect($refererUri && $refererUri != $request->getUri() ? $refererUri : $this->generateUrl('sonata_admin_dashboard'));
        }

        return $response;
    }

    /**
     * @param array $data
     *
     * @return Response
     */
    protected function renderLogin(array $data)
    {
        return $this->render('SonataUserBundle:Admin:Security/login.html.twig', array_merge($data, array(
            'base_template' => $this->get('sonata.admin.pool')->getTemplate('layout'),
            'admin_pool'    => $this->get('sonata.admin.pool'),
            'reset_route'   => $this->generateUrl('sonata_user_admin_resetting_request'),
        )));
    }
}
