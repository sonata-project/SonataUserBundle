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

use Sonata\UserBundle\Security\Authorization\Voter\UserAclVoter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $containerConfigurator->services()

        ->set('security.acl.voter.user_permissions', UserAclVoter::class)
            ->tag('monolog.logger', ['channel' => 'security'])
            ->tag('security.voter', ['priority' => 255])
            ->args([
                new ReferenceConfigurator('security.acl.provider'),
                new ReferenceConfigurator('security.acl.object_identity_retrieval_strategy'),
                new ReferenceConfigurator('security.acl.security_identity_retrieval_strategy'),
                new ReferenceConfigurator('security.acl.permission.map'),
                (new ReferenceConfigurator('logger'))->nullOnInvalid(),
            ]);
};
