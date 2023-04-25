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

use Sonata\UserBundle\Form\Type\RolesMatrixType;
use Sonata\UserBundle\Security\RolesBuilder\AdminRolesBuilder;
use Sonata\UserBundle\Security\RolesBuilder\MatrixRolesBuilder;
use Sonata\UserBundle\Security\RolesBuilder\SecurityRolesBuilder;
use Sonata\UserBundle\Twig\RolesMatrixExtension;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('sonata.user.matrix_roles_builder', MatrixRolesBuilder::class)
            ->args([
                service('security.token_storage'),
                service('sonata.user.admin_roles_builder'),
                service('sonata.user.security_roles_builder'),
            ])

        ->set('sonata.user.admin_roles_builder', AdminRolesBuilder::class)
            ->args([
                service('security.authorization_checker'),
                service('sonata.admin.pool'),
                service('sonata.admin.configuration'),
                service('translator'),
            ])

        ->set('sonata.user.security_roles_builder', SecurityRolesBuilder::class)
            ->args([
                service('security.authorization_checker'),
                service('sonata.admin.configuration'),
                service('translator'),
                param('security.role_hierarchy.roles'),
            ])

        ->set('sonata.user.form.roles_matrix_type', RolesMatrixType::class)
            ->public()
            ->tag('form.type')
            ->args([
                service('sonata.user.matrix_roles_builder'),
            ])

        ->set('sonata.user.roles_matrix_extension', RolesMatrixExtension::class)
            ->tag('twig.extension')
            ->args([
                service('sonata.user.matrix_roles_builder'),
            ]);
};
