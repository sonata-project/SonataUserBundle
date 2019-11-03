<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Security;

use Sonata\UserBundle\Model\FOSUserInterface;
use Symfony\Component\HttpFoundation\Response;

interface LoginManagerInterface
{
    /**
     * @param string        $firewallName
     * @param FOSUserInterface $user
     * @param Response|null $response
     */
    public function logInUser($firewallName, FOSUserInterface $user, Response $response = null);
}
