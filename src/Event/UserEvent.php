<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Event;

use Sonata\UserBundle\Model\FOSUserInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class UserEvent extends Event
{
    /**
     * @var null|Request
     */
    protected $request;

    /**
     * @var FOSUserInterface
     */
    protected $user;

    /**
     * UserEvent constructor.
     *
     * @param FOSUserInterface $user
     * @param Request|null  $request
     */
    public function __construct(FOSUserInterface $user, Request $request = null)
    {
        $this->user = $user;
        $this->request = $request;
    }

    /**
     * @return FOSUserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
