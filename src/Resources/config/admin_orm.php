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

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('sonata.user.admin.user')
            ->tag('sonata.admin', [
                'model_class' => '%sonata.user.user.class%',
                'controller' => '%sonata.user.admin.user.controller%',
                'manager_type' => 'orm',
                'group' => 'sonata_user',
                'label' => 'users',
                'translation_domain' => 'SonataUserBundle',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'icon' => '<i class=\'fa fa-users\'></i>',
            ])
            ->args([
                new ReferenceConfigurator('sonata.user.manager.user'),
            ]);
};
