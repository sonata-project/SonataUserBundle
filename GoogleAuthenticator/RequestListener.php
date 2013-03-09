<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\GoogleAuthenticator;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

class RequestListener
{
    protected $helper;

    protected $securityContext;

    /**
     * @param Helper                                                     $helper
     * @param \Symfony\Component\Security\Core\SecurityContextInterface  $securityContext
     */
    public function __construct(Helper $helper, SecurityContextInterface $securityContext)
    {
        $this->helper = $helper;
        $this->securityContext = $securityContext;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     * @return
     */
    public function onCoreRequest(GetResponseEvent $event)
    {
        $token = $this->securityContext->getToken();

        if (!$token) {
            return;
        }

        if (!$token instanceof UsernamePasswordToken) {
            return;
        }

        $key     = $this->helper->getSessionKey($this->securityContext->getToken());
        $request = $event->getRequest();
        $session = $event->getRequest()->getSession();
        $user    = $this->securityContext->getToken()->getUser();

        if (!$session->has($key)) {
            return;
        }

        if ($session->get($key) === true) {
            return;
        }

        $state = 'init';
        if ($request->getMethod() == 'POST') {
            if ($this->helper->checkCode($user, $request->get('_code')) == true) {
                $session->set($key, true);

                return;
            }

            $state = 'error';
        }

        $requestEvent = new RequestEvent($state);

        $event->getDispatcher()->dispatch(RendererEvents::DISPLAY_FORM, $requestEvent);

        $event->setResponse($requestEvent->getResponse());
    }
}
