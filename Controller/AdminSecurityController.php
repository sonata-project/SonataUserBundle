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

class AdminSecurityController extends SecurityController
{
    protected function renderLogin (array $data)
    {
        $data['base_template'] = $this->container->get('sonata.admin.pool')->getTemplate('layout');
        $data['admin_pool'] = $this->container->get('sonata.admin.pool');

        // Default template is fixed --> only overrideable, but not replaceable
        // return parent::renderLogin($data);

        $template = sprintf('SonataUserBundle:Admin:Security/login.html.%s', $this->container->getParameter('fos_user.template.engine'));

        return $this->container->get('templating')->renderResponse($template, $data);
    }
}
