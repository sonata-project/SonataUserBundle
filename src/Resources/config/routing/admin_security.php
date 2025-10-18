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
            && $trace['args'][0] === __DIR__.'/admin_security.php'
            && $trace['args'][3] === __DIR__.'/admin_security.xml'
        ) {
            @trigger_error(
                sprintf(
                    'The "%s/admin_security.xml" routing configuration is deprecated since sonata-project/user-bundle 5.17. Import "admin_security.php" instead.',
                    __DIR__,
                ),
                \E_USER_DEPRECATED
            );

            break;
        }
    }

    $routes->add('sonata_user_admin_security_login', '/login')
        ->controller('sonata.user.action.login');

    $routes->add('sonata_user_admin_security_check', '/login_check')
        ->controller('sonata.user.action.check_login')
        ->methods(['POST']);

    $routes->add('sonata_user_admin_security_logout', '/logout')
        ->controller('sonata.user.action.logout');
};
