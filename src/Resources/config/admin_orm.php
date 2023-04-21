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

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('sonata.user.admin.user')
            ->tag('sonata.admin', [
                'model_class' => (string) param('sonata.user.user.class'),
                'controller' => (string) param('sonata.user.admin.user.controller'),
                'manager_type' => 'orm',
                'group' => 'sonata_user',
                'label' => 'users',
                'translation_domain' => 'SonataUserBundle',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'icon' => '<i class=\'fa fa-users\'></i>',
            ])
            ->args([
                service('sonata.user.manager.user'),
            ]);
};
