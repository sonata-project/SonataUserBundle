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

// NEXT_MAJOR: remove this file
@trigger_error(
    'The '.__NAMESPACE__.'\DashboardController class is deprecated since version 4.3.0 and will be removed in 5.0.'
    .' Use '.__NAMESPACE__.'\ProfileDashboardAction instead.',
    E_USER_DEPRECATED
);

use Sonata\UserBundle\Action\ProfileDashboardAction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

final class DashboardController extends Controller
{
    public function dashboardAction(): Response
    {
        /** @var ProfileDashboardAction $profileLoginDashboardAction */
        $profileLoginDashboardAction = $this->container->get(ProfileDashboardAction::class);

        $profileLoginDashboardAction();
    }
}
