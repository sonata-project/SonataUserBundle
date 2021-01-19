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

namespace Sonata\UserBundle\Controller;

use Sonata\UserBundle\Action\CheckEmailAction;
use Sonata\UserBundle\Action\RequestAction;
use Sonata\UserBundle\Action\ResetAction;
use Sonata\UserBundle\Action\SendEmailAction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// NEXT_MAJOR: remove this file
@trigger_error(
    'The '.__NAMESPACE__.'\AdminResettingController class is deprecated since version 4.3.0 and will be removed in 5.0.'
    .' Use '.RequestAction::class.', '.CheckEmailAction::class.', '.ResetAction::class.' or '.SendEmailAction::class.' instead.',
    \E_USER_DEPRECATED
);

class AdminResettingController extends Controller
{
    /**
     * @return Response
     */
    public function requestAction()
    {
        /** @var RequestAction $requestAction */
        $requestAction = $this->container->get(RequestAction::class);

        return $requestAction($this->getCurrentRequest());
    }

    /**
     * @return Response
     */
    public function sendEmailAction(Request $request)
    {
        /** @var SendEmailAction $sendEmailAction */
        $sendEmailAction = $this->container->get(SendEmailAction::class);

        return $sendEmailAction($this->getCurrentRequest());
    }

    /**
     * @return Response
     */
    public function checkEmailAction(Request $request)
    {
        /** @var CheckEmailAction $checkEmailAction */
        $checkEmailAction = $this->container->get(CheckEmailAction::class);

        return $checkEmailAction($this->getCurrentRequest());
    }

    /**
     * @return Response
     */
    public function resetAction(Request $request, string $token)
    {
        /** @var ResetAction $resetAction */
        $resetAction = $this->container->get(ResetAction::class);

        return $resetAction($this->getCurrentRequest(), $token);
    }

    private function getCurrentRequest(): Request
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }
}
