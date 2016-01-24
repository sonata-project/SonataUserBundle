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

use FOS\UserBundle\Controller\SecurityController;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class SecurityFOSUser1Controller.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class SecurityFOSUser1Controller extends SecurityController
{
    /**
     * {@inheritdoc}
     */
    public function loginAction()
    {
        if ($this->container->has('security.token_storage')) {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
        } else {
            // BC for SF < 2.7
            $user = $this->container->get('security.context')->getToken()->getUser();
        }

        if ($user instanceof UserInterface) {
            $this->container->get('session')->getFlashBag()->set('sonata_user_error', 'sonata_user_already_authenticated');
            $url = $this->container->get('router')->generate('sonata_user_profile_show');

            return new RedirectResponse($url);
        }

        return parent::loginAction();
    }
}
