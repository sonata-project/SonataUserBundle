<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SecurityFOSUser2Controller extends SecurityController
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        $user = $this->getUser();

        $token = $this->get('security.token_storage')->getToken();

        if ($token && $token->getUser() instanceof UserInterface) {
            $this->addFlash('sonata_user_error', 'sonata_user_already_authenticated');
            $url = $this->get('router')->generate('sonata_user_profile_show');

            return new RedirectResponse($url);
        }

        return parent::loginAction($request);
    }
}
