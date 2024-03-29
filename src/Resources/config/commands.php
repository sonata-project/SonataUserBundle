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

use Sonata\UserBundle\Command\ActivateUserCommand;
use Sonata\UserBundle\Command\ChangePasswordCommand;
use Sonata\UserBundle\Command\CreateUserCommand;
use Sonata\UserBundle\Command\DeactivateUserCommand;
use Sonata\UserBundle\Command\DemoteUserCommand;
use Sonata\UserBundle\Command\PromoteUserCommand;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('sonata.user.command.activate_user', ActivateUserCommand::class)
            ->tag('console.command')
            ->args([
                service('sonata.user.manager.user'),
            ])

        ->set('sonata.user.command.change_password', ChangePasswordCommand::class)
            ->tag('console.command')
            ->args([
                service('sonata.user.manager.user'),
            ])

        ->set('sonata.user.command.create_user', CreateUserCommand::class)
            ->tag('console.command')
            ->args([
                service('sonata.user.manager.user'),
            ])

        ->set('sonata.user.command.deactivate_user', DeactivateUserCommand::class)
            ->tag('console.command')
            ->args([
                service('sonata.user.manager.user'),
            ])

        ->set('sonata.user.command.promote_user', PromoteUserCommand::class)
            ->tag('console.command')
            ->args([
                service('sonata.user.manager.user'),
            ])

        ->set('sonata.user.command.demote_user', DemoteUserCommand::class)
            ->tag('console.command')
            ->args([
                service('sonata.user.manager.user'),
            ]);
};
