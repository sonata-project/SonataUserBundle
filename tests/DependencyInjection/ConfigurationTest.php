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

namespace Sonata\UserBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Admin\Entity\UserAdmin;
use Sonata\UserBundle\DependencyInjection\Configuration;
use Sonata\UserBundle\Entity\BaseUser;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    public function testDefault(): void
    {
        $this->assertProcessedConfigurationEquals([
            [],
        ], [
            'security_acl' => false,
            'manager_type' => 'orm',
            'class' => [
                'user' => BaseUser::class,
            ],
            'admin' => [
                'user' => [
                    'class' => UserAdmin::class,
                    'controller' => '%sonata.admin.configuration.default_controller%',
                    'translation' => 'SonataUserBundle',
                ],
            ],
            'profile' => [
                'default_avatar' => 'bundles/sonatauser/default_avatar.png',
            ],
            'mailer' => 'sonata.user.mailer.default',
            'resetting' => [
                'retry_ttl' => 7200,
                'token_ttl' => 86400,
                'email' => [
                    'template' => '@SonataUser/Admin/Security/Resetting/email.html.twig',
                ],
            ],
        ]);
    }
}
