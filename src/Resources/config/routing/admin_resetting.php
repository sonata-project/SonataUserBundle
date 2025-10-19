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

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Loader\XmlFileLoader;

return static function (RoutingConfigurator $routes) {
    foreach (debug_backtrace() as $trace) {
        if (isset($trace['object'], $trace['args'])
            && $trace['object'] instanceof XmlFileLoader
            && $trace['args'][0] === __DIR__.'/admin_resetting.php'
            && $trace['args'][3] === __DIR__.'/admin_resetting.xml'
        ) {
            @trigger_error(
                sprintf(
                    'The "%s/admin_resetting.xml" routing configuration is deprecated since sonata-project/user-bundle 5.17. Import "admin_resetting.php" instead.',
                    __DIR__,
                ),
                \E_USER_DEPRECATED
            );

            break;
        }
    }

    $routes->add('sonata_user_admin_resetting_request', '/request')
        ->controller('sonata.user.action.request')
        ->methods(['GET', 'POST']);

    $routes->add('sonata_user_admin_resetting_check_email', '/check-email')
        ->controller('sonata.user.action.check_email')
        ->methods(['GET']);

    $routes->add('sonata_user_admin_resetting_reset', '/reset/{token}')
        ->controller('sonata.user.action.reset')
        ->methods(['GET', 'POST']);
};
