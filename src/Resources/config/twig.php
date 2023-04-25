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

use Sonata\UserBundle\Twig\GlobalVariables;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('sonata.user.twig.global', GlobalVariables::class)
            ->args([
                service('sonata.admin.pool')->nullOnInvalid(),
                abstract_arg('default avatar'),
                abstract_arg('impersonating enabled'),
                abstract_arg('impersonating route'),
                abstract_arg('impersonating route parameters'),
            ]);
};
