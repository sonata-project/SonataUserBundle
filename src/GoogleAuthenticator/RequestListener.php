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

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Twig\Environment;

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
     * NEXT_MAJOR: Remove this property.
     *
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * NEXT_MAJOR: Remove `$templating` argument and make `$twig` argument mandatory.
     *
     * @param EngineInterface|Environment $templating
     */
    public function __construct(Helper $helper, TokenStorageInterface $tokenStorage, ?object $templating = null, ?Environment $twig = null)
    {
        $this->helper = $helper;
        $this->tokenStorage = $tokenStorage;
        // $this->twig = $twig;
        // NEXT_MAJOR: Uncomment the previous assignment and remove the following lines in this method.

        if ($templating instanceof EngineInterface) {
            $this->templating = $templating;

            @trigger_error(sprintf(
                'Passing an instance of %s as argument 3 to "%s()" is deprecated since sonata-project/user-bundle 4.5 and will only accept an instance of %s in version 5.0.',
                EngineInterface::class,
                __METHOD__,
                Environment::class
            ), E_USER_DEPRECATED);
        } elseif ($templating instanceof Environment) {
            $this->twig = $templating;
        } else {
            throw new \TypeError(sprintf(
                'Argument 3 passed to %s() must be an instance of %s or %s, %s given.',
                __METHOD__,
                Environment::class,
                EngineInterface::class,
                \get_class($templating)
            ));
        }

        if (null === $this->twig) {
            $this->twig = $twig;
        }
    }

    public function onCoreRequest(GetResponseEvent $event): void
    {
        if (HttpKernel::MASTER_REQUEST !== $event->getRequestType()) {
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

        // NEXT_MAJOR: Remove the following check and the `else` condition
        if ($this->twig) {
            $event->setResponse(new Response($this->twig->render('@SonataUser/Admin/Security/login.html.twig', [
                'base_template' => '@SonataAdmin/standard_layout.html.twig',
                'error' => [],
                'state' => $state,
                'two_step_submit' => true,
            ])));
        } else {
            $event->setResponse($this->templating->renderResponse('@SonataUser/Admin/Security/login.html.twig', [
                'base_template' => '@SonataAdmin/standard_layout.html.twig',
                'error' => [],
                'state' => $state,
                'two_step_submit' => true,
            ]));
        }
    }
}
