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

use Sonata\UserBundle\Form\Type\ResetPasswordRequestFormType;
use Sonata\UserBundle\Form\Type\ResettingFormType;
use Sonata\UserBundle\Form\Type\RolesMatrixType;
use Sonata\UserBundle\Security\RolesBuilder\AdminRolesBuilder;
use Sonata\UserBundle\Security\RolesBuilder\MatrixRolesBuilder;
use Sonata\UserBundle\Security\RolesBuilder\SecurityRolesBuilder;
use Sonata\UserBundle\Twig\RolesMatrixExtension;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('sonata.user.form.type.resetting', ResettingFormType::class)
            ->tag('form.type', ['alias' => 'sonata_user_resetting'])
            ->args([
                param('sonata.user.user.class'),
            ])

        ->set('sonata.user.form.type.reset_password_request', ResetPasswordRequestFormType::class)
            ->tag('form.type', ['alias' => 'sonata_user_reset_password_request'])

        ->set('sonata.user.matrix_roles_builder', MatrixRolesBuilder::class)
            ->args([
                service('security.token_storage'),
                service('sonata.user.admin_roles_builder')->nullOnInvalid(),
                service('sonata.user.security_roles_builder')->nullOnInvalid(),
            ])

        ->set('sonata.user.security_roles_builder', SecurityRolesBuilder::class)
            ->args([
                service('security.authorization_checker'),
                service('sonata.admin.configuration')->nullOnInvalid(),
                service('translator'),
                param('security.role_hierarchy.roles'),
            ])

        ->set('sonata.user.form.roles_matrix_type', RolesMatrixType::class)
            ->public()
            ->tag('form.type')
            ->args([
                service('sonata.user.matrix_roles_builder'),
            ])
    ;
};
