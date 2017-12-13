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
use Sonata\UserBundle\DependencyInjection\Configuration;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function getConfiguration()
    {
        return new Configuration();
    }

    public function testWithoutClassNode(): void
    {
        $this->assertConfigurationIsInvalid([[]], 'The child node "class" at path "sonata_user" must be configured.');
    }

    public function testWithClassNode(): void
    {
        $this->assertConfigurationIsValid([[
            'class' => [
                'user' => 'FooBundle\User',
                'group' => 'FooBundle\Group',
            ],
        ]]);
    }

    public function testDefault(): void
    {
        $this->assertProcessedConfigurationEquals([
            [
                'class' => [
                    'user' => 'FooBundle\User',
                    'group' => 'FooBundle\Group',
                ],
            ],
        ], [
            'security_acl' => false,
            'table' => [
                'user_group' => 'fos_user_user_group',
            ],
            'google_authenticator' => [
                'enabled' => false,
            ],
            'manager_type' => 'orm',
            'class' => [
                'user' => 'FooBundle\User',
                'group' => 'FooBundle\Group',
            ],
            'admin' => [
                'user' => [
                    'class' => 'Sonata\UserBundle\Admin\Entity\UserAdmin',
                    'controller' => 'SonataAdminBundle:CRUD',
                    'translation' => 'SonataUserBundle',
                ],
                'group' => [
                    'class' => 'Sonata\UserBundle\Admin\Entity\GroupAdmin',
                    'controller' => 'SonataAdminBundle:CRUD',
                    'translation' => 'SonataUserBundle',
                ],
            ],
            'profile' => [
                'default_avatar' => 'bundles/sonatauser/default_avatar.png',
            ],
        ]);
    }
}
