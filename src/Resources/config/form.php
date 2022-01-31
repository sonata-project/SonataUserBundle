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

use Sonata\UserBundle\Form\Type\ResetPasswordRequestFormType;
use Sonata\UserBundle\Form\Type\ResettingFormType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('sonata.user.form.type.resetting', ResettingFormType::class)
            ->tag('form.type', ['alias' => 'sonata_user_resetting'])
            ->args([
                '%sonata.user.user.class%',
            ])

        ->set('sonata.user.form.type.reset_password_request', ResetPasswordRequestFormType::class)
            ->tag('form.type', ['alias' => 'sonata_user_reset_password_request']);
};
