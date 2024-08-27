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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sonata\UserBundle\Action\CheckEmailAction;
use Sonata\UserBundle\Action\RequestAction;
use Sonata\UserBundle\Action\ResetAction;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()
        ->set('sonata.user.action.request', RequestAction::class)
        ->public()
        ->args([
            service('twig'),
            service('router'),
            service('security.authorization_checker'),
            service('sonata.admin.pool'),
            service('sonata.admin.global_template_registry'),
            service('form.factory'),
            service('sonata.user.manager.user'),
            service('sonata.user.mailer'),
            service('sonata.user.util.token_generator'),
            abstract_arg('retry ttl'),
        ])

        ->set('sonata.user.action.check_email', CheckEmailAction::class)
        ->public()
        ->args([
            service('twig'),
            service('sonata.admin.pool'),
            service('sonata.admin.global_template_registry'),
            abstract_arg('token ttl'),
        ])

        ->set('sonata.user.action.reset', ResetAction::class)
        ->public()
        ->args([
            service('twig'),
            service('router'),
            service('security.authorization_checker'),
            service('sonata.admin.pool'),
            service('sonata.admin.global_template_registry'),
            service('form.factory'),
            service('sonata.user.manager.user'),
            service('translator'),
            abstract_arg('token ttl'),
        ]);
};
