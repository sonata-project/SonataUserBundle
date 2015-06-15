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

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\ChangePasswordController;

/**
 * Class ChangePasswordFOSUser1Controller
 *
 * This class is inspired from the FOS Change Password Controller
 *
 * @package Sonata\UserBundle\Controller
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class ChangePasswordFOSUser2Controller extends ChangePasswordController
{

}
