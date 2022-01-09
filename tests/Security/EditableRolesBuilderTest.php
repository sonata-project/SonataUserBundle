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

namespace Sonata\UserBundle\Tests\Security\Authorization\Voter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface;
use Sonata\AdminBundle\SonataConfiguration;
use Sonata\UserBundle\Security\EditableRolesBuilder;
use Sonata\UserBundle\Security\EditableRolesBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditableRolesBuilderTest extends TestCase
{
    /**
     * @var MockObject&TokenStorageInterface
     */
    private MockObject $tokenStorage;

    /**
     * @var MockObject&AuthorizationCheckerInterface
     */
    private MockObject $authorizationChecker;

    private Container $container;

    private Pool $pool;

    private SonataConfiguration $configuration;

    /**
     * @var Stub&TranslatorInterface
     */
    private Stub $translator;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);

        $this->container = new Container();
        $this->pool = new Pool($this->container);
        $this->configuration = new SonataConfiguration('title', 'logo', [
            'confirm_exit' => true,
            'default_group' => 'group',
            'default_icon' => 'icon',
            'default_label_catalogue' => 'label_catalogue',
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
            'skin' => 'blue',
            'sort_admins' => true,
            'stylesheets' => [],
            'use_bootlint' => true,
            'use_icheck' => true,
            'use_select2' => true,
            'use_stickyforms' => true,
        ]);

        $this->translator = $this->createStub(TranslatorInterface::class);
    }

    public function testRolesFromHierarchy(): void
    {
        $this->tokenStorage->method('getToken')->willReturn(
            $this->createStub(TokenInterface::class)
        );
        $this->authorizationChecker->method('isGranted')->willReturn(true);

        $builder = $this->getEditableRolesBuilder([
            'ROLE_ADMIN' => [
                0 => 'ROLE_USER',
            ],
            'ROLE_SUPER_ADMIN' => [
                0 => 'ROLE_USER',
                1 => 'ROLE_SONATA_ADMIN',
                2 => 'ROLE_ADMIN',
                3 => 'ROLE_ALLOWED_TO_SWITCH',
                4 => 'ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT',
                5 => 'ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT',
            ],
            'SONATA' => [],
        ]);

        $roles = $builder->getRoles();
        $rolesReadOnly = $builder->getRolesReadOnly();

        static::assertEmpty($rolesReadOnly);
        static::assertSame([
            'ROLE_ADMIN' => 'ROLE_ADMIN: ROLE_USER',
            'ROLE_USER' => 'ROLE_USER',
            'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN: ROLE_USER, ROLE_SONATA_ADMIN, ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH, ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT, ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT',
            'ROLE_SONATA_ADMIN' => 'ROLE_SONATA_ADMIN',
            'ROLE_ALLOWED_TO_SWITCH' => 'ROLE_ALLOWED_TO_SWITCH',
            'ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT' => 'ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT',
            'ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT' => 'ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT',
            'SONATA' => 'SONATA: ',
        ], $roles);
    }

    public function testRolesFromAdminWithMasterAdmin(): void
    {
        $securityHandler = $this->createMock(SecurityHandlerInterface::class);
        $securityHandler->expects(static::exactly(2))->method('getBaseRole')->willReturn('ROLE_FOO_%s');

        $admin = $this->createMock(AdminInterface::class);
        $admin->expects(static::exactly(2))->method('isGranted')->willReturn(true);
        $admin->expects(static::exactly(2))->method('getSecurityInformation')->willReturn(['GUEST' => [0 => 'VIEW', 1 => 'LIST'], 'STAFF' => [0 => 'EDIT', 1 => 'LIST', 2 => 'CREATE'], 'EDITOR' => [0 => 'OPERATOR', 1 => 'EXPORT'], 'ADMIN' => [0 => 'MASTER']]);
        $admin->expects(static::exactly(2))->method('getSecurityHandler')->willReturn($securityHandler);

        $this->tokenStorage->method('getToken')->willReturn(
            $this->createStub(TokenInterface::class)
        );
        $this->authorizationChecker->method('isGranted')->willReturn(true);
        $this->container->set('myadmin', $admin);

        $pool = new Pool($this->container, ['myadmin']);
        $builder = $this->getEditableRolesBuilder([], $pool);

        $roles = $builder->getRoles();
        $rolesReadOnly = $builder->getRolesReadOnly();

        static::assertEmpty($rolesReadOnly);
        static::assertSame([
            'ROLE_FOO_GUEST' => 'ROLE_FOO_GUEST',
            'ROLE_FOO_STAFF' => 'ROLE_FOO_STAFF',
            'ROLE_FOO_EDITOR' => 'ROLE_FOO_EDITOR',
            'ROLE_FOO_ADMIN' => 'ROLE_FOO_ADMIN',
        ], $roles);
    }

    public function testWithNoSecurityToken(): void
    {
        $this->tokenStorage->method('getToken')->willReturn(null);
        $this->authorizationChecker->method('isGranted')->willReturn(false);

        $builder = $this->getEditableRolesBuilder();

        $roles = $builder->getRoles();
        $rolesReadOnly = $builder->getRolesReadOnly();

        static::assertEmpty($roles);
        static::assertEmpty($rolesReadOnly);
    }

    /**
     * @param array<string, array<string>> $rolesHierarchy
     */
    private function getEditableRolesBuilder(array $rolesHierarchy = [], ?Pool $pool = null): EditableRolesBuilderInterface
    {
        return new EditableRolesBuilder(
            $this->tokenStorage,
            $this->authorizationChecker,
            $pool ?? $this->pool,
            $this->configuration,
            $this->translator,
            $rolesHierarchy
        );
    }
}
