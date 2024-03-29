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

namespace Sonata\UserBundle;

use Sonata\UserBundle\DependencyInjection\Compiler\GlobalVariablesCompilerPass;
use Sonata\UserBundle\DependencyInjection\Compiler\RolesMatrixCompilerPass;
use Sonata\UserBundle\DependencyInjection\Compiler\ValidationCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SonataUserBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new GlobalVariablesCompilerPass());
        $container->addCompilerPass(new RolesMatrixCompilerPass());
        $container->addCompilerPass(new ValidationCompilerPass());
    }
}
