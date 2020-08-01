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

use Sonata\UserBundle\Action\CheckLoginAction;
use Sonata\UserBundle\Action\LoginAction;
use Sonata\UserBundle\Action\LogoutAction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// NEXT_MAJOR: remove this file
@trigger_error(
    'The '.__NAMESPACE__.'\AdminSecurityController class is deprecated since version 4.3.0 and will be removed in 5.0.'
    .' Use '.CheckLoginAction::class.', '.LoginAction::class.' or '.LogoutAction::class.' instead.',
    E_USER_DEPRECATED
);

class AdminSecurityController extends Controller
{
    public function loginAction(Request $request): Response
    {
        /** @var LoginAction $loginAction */
        $loginAction = $this->container->get(LoginAction::class);

        return $loginAction($request);
    }

    public function checkAction(): void
    {
        /** @var CheckLoginAction $checkLoginAction */
        $checkLoginAction = $this->container->get(CheckLoginAction::class);

        $checkLoginAction();
    }

    public function logoutAction(): void
    {
        /** @var LogoutAction $logoutAction */
        $logoutAction = $this->container->get(LogoutAction::class);

        $logoutAction();
    }
}
