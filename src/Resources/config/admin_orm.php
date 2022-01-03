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

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('sonata.user.admin.user', '%sonata.user.admin.user.class%')
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'sonata_user',
                'label' => 'users',
                'label_catalogue' => 'SonataUserBundle',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'icon' => '<i class=\'fa fa-users\'></i>',
            ])
            ->args([
                '',
                '%sonata.user.user.class%',
                '%sonata.user.admin.user.controller%',
            ])
            ->call('setTranslationDomain', ['%sonata.user.admin.user.translation_domain%']);
};
