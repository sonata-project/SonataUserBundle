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
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\UserBundle\Admin\Entity\GroupAdmin;
use Sonata\UserBundle\Admin\Entity\UserAdmin;
use Sonata\UserBundle\DependencyInjection\Configuration;
use Sonata\UserBundle\Entity\BaseGroup;
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
            'table' => [
                'user_group' => 'fos_user_user_group',
            ],
            'google_authenticator' => [
                'enabled' => false,
                'ip_white_list' => ['127.0.0.1'],
                'forced_for_role' => ['ROLE_ADMIN'],
            ],
            'manager_type' => 'orm',
            'class' => [
                'user' => BaseUser::class,
                'group' => BaseGroup::class,
            ],
            'admin' => [
                'user' => [
                    'class' => UserAdmin::class,
                    'controller' => CRUDController::class,
                    'translation' => 'SonataUserBundle',
                ],
                'group' => [
                    'class' => GroupAdmin::class,
                    'controller' => CRUDController::class,
                    'translation' => 'SonataUserBundle',
                ],
            ],
            'profile' => [
                'default_avatar' => 'bundles/sonatauser/default_avatar.png',
                'template' => '@SonataUser/Profile/action.html.twig',
                'menu_builder' => 'sonata.user.profile.menu_builder.default',
                'blocks' => [
                    [
                        'position' => 'left',
                        'type' => 'sonata.user.block.account',
                        'settings' => [
                            'template' => '@SonataUser/Block/account_dashboard.html.twig',
                        ],
                    ],
                    [
                        'position' => 'right',
                        'type' => 'sonata.block.service.text',
                        'settings' => ['content' => "<h2>Welcome!</h2> <p>This is a sample user profile dashboard, feel free to override it in the configuration! Want to make this text dynamic? For instance display the user's name? Create a dedicated block and edit the configuration!</p>"],
                    ],
                ],
                'menu' => [
                    [
                        'route' => 'sonata_user_profile_dashboard',
                        'label' => 'link_show_profile',
                        'domain' => 'SonataUserBundle',
                        'route_parameters' => [],
                    ],
                ],
            ],
            'mailer' => 'sonata.user.mailer.default',
        ]);
    }
}
