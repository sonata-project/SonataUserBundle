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

namespace Sonata\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Flex\Recipe;

/**
 * Checks to see if the mailer service exists.
 *
 * @author Ryan Weaver <ryan@knpuniversity.com>
 */
class CheckForMailerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // if the mailer isn't needed, then no error needed
        if (!$container->has('sonata.user.mailer')) {
            return;
        }

        // the mailer exists, so all is good
        if ($container->has('mailer')) {
            return;
        }

        if ($container->findDefinition('sonata.user.mailer')->hasTag('sonata.user.requires_swift')) {
            $message = 'A feature you activated in SonataUserBundle requires the "mailer" service to be available.';

            if (class_exists(Recipe::class)) {
                $message .= ' Run "composer require swiftmailer-bundle" to install SwiftMailer or configure a different mailer in "config/packages/sonata_user.yaml".';
            }

            throw new \LogicException($message);
        }
    }
}
