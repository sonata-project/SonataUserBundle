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

use Sonata\UserBundle\Security\Authorization\Voter\UserAclVoter;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('security.acl.voter.user_permissions', UserAclVoter::class)
            ->tag('monolog.logger', ['channel' => 'security'])
            ->tag('security.voter', ['priority' => 255])
            ->args([
                service('security.acl.provider'),
                service('security.acl.object_identity_retrieval_strategy'),
                service('security.acl.security_identity_retrieval_strategy'),
                service('security.acl.permission.map'),
                service('logger')->nullOnInvalid(),
            ]);
};
