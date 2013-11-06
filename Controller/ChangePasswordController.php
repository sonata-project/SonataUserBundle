<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Controller;

use FOS\UserBundle\Controller\ChangePasswordController as BaseController;
use FOS\UserBundle\Model\UserInterface;

/**
 * Class ChangePasswordController
 *
 * @package Sonata\UserBundle\Controller
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class ChangePasswordController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function getRedirectionUrl(UserInterface $user)
    {
        return $this->container->get('router')->generate('sonata_user_profile_show');
    }
}
