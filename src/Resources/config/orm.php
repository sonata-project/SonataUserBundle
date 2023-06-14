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

use Sonata\UserBundle\Entity\UserManager;
use Sonata\UserBundle\Listener\UserListener;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('sonata.user.manager.user', UserManager::class)
            ->args([
                param('sonata.user.user.class'),
                service('doctrine'),
                service('sonata.user.util.canonical_fields_updater'),
                service('security.password_hasher'),
            ])

        ->set('sonata.user.listener.user', UserListener::class)
            ->tag('doctrine.event_listener', [
                'event' => 'prePersist',
            ])
            ->tag('doctrine.event_listener', [
                'event' => 'preUpdate',
            ])
            ->args([
                service('sonata.user.util.canonical_fields_updater'),
                service('sonata.user.manager.user'),
            ]);
};
