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

use Sonata\UserBundle\Form\Type\RolesMatrixType;
use Sonata\UserBundle\Security\RolesBuilder\AdminRolesBuilder;
use Sonata\UserBundle\Security\RolesBuilder\MatrixRolesBuilder;
use Sonata\UserBundle\Security\RolesBuilder\SecurityRolesBuilder;
use Sonata\UserBundle\Twig\RolesMatrixExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('sonata.user.matrix_roles_builder', MatrixRolesBuilder::class)
            ->args([
                new ReferenceConfigurator('security.token_storage'),
                new ReferenceConfigurator('sonata.user.admin_roles_builder'),
                new ReferenceConfigurator('sonata.user.security_roles_builder'),
            ])

        ->set('sonata.user.admin_roles_builder', AdminRolesBuilder::class)
            ->args([
                new ReferenceConfigurator('security.authorization_checker'),
                new ReferenceConfigurator('sonata.admin.pool'),
                new ReferenceConfigurator('sonata.admin.configuration'),
                new ReferenceConfigurator('translator'),
            ])

        ->set('sonata.user.security_roles_builder', SecurityRolesBuilder::class)
            ->args([
                new ReferenceConfigurator('security.authorization_checker'),
                new ReferenceConfigurator('sonata.admin.configuration'),
                new ReferenceConfigurator('translator'),
                '%security.role_hierarchy.roles%',
            ])

        ->set('sonata.user.form.roles_matrix_type', RolesMatrixType::class)
            ->public()
            ->tag('form.type')
            ->args([
                new ReferenceConfigurator('sonata.user.matrix_roles_builder'),
            ])

        ->set('sonata.user.roles_matrix_extension', RolesMatrixExtension::class)
            ->tag('twig.extension')
            ->args([
                new ReferenceConfigurator('sonata.user.matrix_roles_builder'),
            ]);
};
