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

namespace Sonata\UserBundle\Tests\Security\RolesBuilder;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface;
use Sonata\AdminBundle\SonataConfiguration;
use Sonata\UserBundle\Security\RolesBuilder\AdminRolesBuilder;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class AdminRolesBuilderTest extends TestCase
{
    /**
     * @var MockObject&SecurityHandlerInterface
     */
    private MockObject $securityHandler;

    /**
     * @var MockObject&AuthorizationCheckerInterface
     */
    private MockObject $authorizationChecker;

    /**
     * @var MockObject&AdminInterface<object>
     */
    private MockObject $admin;

    private Pool $pool;

    private SonataConfiguration $configuration;

    /**
     * @var MockObject&TranslatorInterface
     */
    private MockObject $translator;

    /**
     * @var array<string, string[]>
     */
    private array $securityInformation = [
        'GUEST' => ['VIEW', 'LIST'],
        'STAFF' => ['EDIT', 'LIST', 'CREATE'],
        'EDITOR' => ['OPERATOR', 'EXPORT'],
        'ADMIN' => ['MASTER'],
    ];

    protected function setUp(): void
    {
        $this->securityHandler = $this->createMock(SecurityHandlerInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->admin = $this->createMock(AdminInterface::class);

        $container = new Container();
        $container->set('sonata.admin.bar', $this->admin);

        $adminGroups = [
            'bar' => [
                'label' => 'Bar',
                'translation_domain' => '',
                'icon' => '<i class="fas fa-edit"></i>',
                'items' => [['admin' => 'sonata.admin.bar', 'roles' => [], 'route_absolute' => false, 'route_params' => []]],
                'keep_open' => false,
                'on_top' => false,
                'roles' => [],
            ],
        ];

        $this->pool = new Pool($container, ['sonata.admin.bar'], $adminGroups);
        $this->configuration = new SonataConfiguration('title', 'logo', [
            'confirm_exit' => true,
            'default_admin_route' => 'show',
            'default_group' => 'group',
            'default_icon' => 'icon',
            'default_translation_domain' => 'label_catalogue',
            'dropdown_number_groups_per_colums' => 1,
            'form_type' => 'horizontal',
            'html5_validate' => true,
            'javascripts' => [],
            'js_debug' => true,
            'list_action_button_content' => 'text',
            'lock_protection' => true,
            'logo_content' => 'text',
            'mosaic_background' => 'background',
            'pager_links' => 1,
            'role_admin' => 'ROLE_ADMIN',
            'role_super_admin' => 'ROLE_SUPER_ADMIN',
            'search' => true,
            'skin' => 'skin-blue',
            'sort_admins' => true,
            'stylesheets' => [],
            'use_bootlint' => true,
            'use_icheck' => true,
            'use_select2' => true,
            'use_stickyforms' => true,
        ]);
        $this->translator = $this->createMock(TranslatorInterface::class);
    }

    public function testGetPermissionLabels(): void
    {
        $this->translator->method('trans')->willReturnCallback(
            static fn (string $key): string => $key
        );

        $this->securityHandler->method('getBaseRole')
            ->willReturn('ROLE_SONATA_FOO_%s');

        $this->admin->method('getSecurityHandler')
            ->willReturn($this->securityHandler);

        $this->admin->method('getTranslator')
            ->willReturn($this->translator);

        $this->admin->method('getSecurityInformation')
            ->willReturn($this->securityInformation);

        $rolesBuilder = new AdminRolesBuilder(
            $this->authorizationChecker,
            $this->pool,
            $this->configuration,
            $this->translator
        );

        $expected = [
            'GUEST' => 'GUEST',
            'STAFF' => 'STAFF',
            'EDITOR' => 'EDITOR',
            'ADMIN' => 'ADMIN',
        ];

        static::assertSame($expected, $rolesBuilder->getPermissionLabels());
    }

    public function testGetRoles(): void
    {
        $this->translator->method('trans')
            ->willReturn('Foo');

        $this->securityHandler->method('getBaseRole')
            ->willReturn('ROLE_SONATA_FOO_%s');

        $this->admin->method('getSecurityHandler')
            ->willReturn($this->securityHandler);

        $this->admin->method('getTranslator')
            ->willReturn($this->translator);

        $this->admin->method('getSecurityInformation')
            ->willReturn($this->securityInformation);

        $this->admin->method('getLabel')
            ->willReturn('Foo');

        $rolesBuilder = new AdminRolesBuilder(
            $this->authorizationChecker,
            $this->pool,
            $this->configuration,
            $this->translator
        );

        $expected = [
            'ROLE_SONATA_FOO_GUEST' => [
                'role' => 'ROLE_SONATA_FOO_GUEST',
                'label' => 'GUEST',
                'role_translated' => 'ROLE_SONATA_FOO_GUEST',
                'is_granted' => false,
                'admin_label' => 'Foo',
                'admin_code' => 'sonata.admin.bar',
                'group_label' => 'Foo',
                'group_code' => 'bar',
            ],
            'ROLE_SONATA_FOO_STAFF' => [
                'role' => 'ROLE_SONATA_FOO_STAFF',
                'label' => 'STAFF',
                'role_translated' => 'ROLE_SONATA_FOO_STAFF',
                'is_granted' => false,
                'admin_label' => 'Foo',
                'admin_code' => 'sonata.admin.bar',
                'group_label' => 'Foo',
                'group_code' => 'bar',
            ],
            'ROLE_SONATA_FOO_EDITOR' => [
                'role' => 'ROLE_SONATA_FOO_EDITOR',
                'label' => 'EDITOR',
                'role_translated' => 'ROLE_SONATA_FOO_EDITOR',
                'is_granted' => false,
                'admin_label' => 'Foo',
                'admin_code' => 'sonata.admin.bar',
                'group_label' => 'Foo',
                'group_code' => 'bar',
            ],
            'ROLE_SONATA_FOO_ADMIN' => [
                'role' => 'ROLE_SONATA_FOO_ADMIN',
                'label' => 'ADMIN',
                'role_translated' => 'ROLE_SONATA_FOO_ADMIN',
                'is_granted' => false,
                'admin_label' => 'Foo',
                'admin_code' => 'sonata.admin.bar',
                'group_label' => 'Foo',
                'group_code' => 'bar',
            ],
        ];

        static::assertSame($expected, $rolesBuilder->getRoles());
    }

    public function testGetAddExcludeAdmins(): void
    {
        $rolesBuilder = new AdminRolesBuilder(
            $this->authorizationChecker,
            $this->pool,
            $this->configuration,
            $this->translator
        );
        $rolesBuilder->addExcludeAdmin('sonata.admin.bar');

        static::assertSame(['sonata.admin.bar'], $rolesBuilder->getExcludeAdmins());
    }
}
