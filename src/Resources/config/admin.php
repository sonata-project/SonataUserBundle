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

use Sonata\UserBundle\Security\RolesBuilder\AdminRolesBuilder;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('sonata.user.admin_roles_builder', AdminRolesBuilder::class)
            ->args([
                service('security.authorization_checker'),
                service('sonata.admin.pool')->nullOnInvalid(),
                service('sonata.admin.configuration')->nullOnInvalid(),
                service('translator'),
            ]);
};
