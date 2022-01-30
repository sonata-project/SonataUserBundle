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

use Sonata\UserBundle\Entity\UserManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;

return static function (ContainerConfigurator $containerConfigurator): void {
    /**
     * TODO: Simplify this when dropping support for Symfony 4.
     */
    $passwordHasherId = class_exists(AuthenticatorManager::class) ? 'security.password_hasher' : 'security.password_encoder';

    // Use "service" function for creating references to services when dropping support for Symfony 4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('sonata.user.manager.user', UserManager::class)
            ->args([
                '%sonata.user.user.class%',
                new ReferenceConfigurator('doctrine'),
                new ReferenceConfigurator('sonata.user.util.canonical_fields_updater'),
                new ReferenceConfigurator($passwordHasherId),
            ]);
};
