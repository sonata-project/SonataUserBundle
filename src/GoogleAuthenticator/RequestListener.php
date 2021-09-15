<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\GoogleAuthenticator;

use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RequestListener
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        Helper $helper,
        TokenStorageInterface $tokenStorage,
        Environment $twig = null
    ) {
        $this->helper = $helper;
        $this->tokenStorage = $tokenStorage;
        $this->twig = $twig;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function onCoreRequest(RequestEvent $event): void
    {
        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return;
        }

        if (!$token instanceof UsernamePasswordToken) {
            return;
        }

        $key = $this->helper->getSessionKey($token);
        $request = $event->getRequest();
        $session = $event->getRequest()->getSession();
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        if (!$session->has($key)) {
            return;
        }

        if (true === $session->get($key)) {
            return;
        }

        $state = 'init';
        if ('POST' === $request->getMethod()) {
            if (true === $this->helper->checkCode($user, $request->get('_code'))) {
                $session->set($key, true);

                return;
            }

            $state = 'error';
        }

        $event->setResponse(new Response($this->twig->render('@SonataUser/Admin/Security/login.html.twig', [
            'base_template' => '@SonataAdmin/standard_layout.html.twig',
            'error' => [],
            'state' => $state,
            'two_step_submit' => true,
        ])));
    }
}
