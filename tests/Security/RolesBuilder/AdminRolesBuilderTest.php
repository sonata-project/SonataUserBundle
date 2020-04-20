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

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface;
use Sonata\UserBundle\Security\RolesBuilder\AdminRolesBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class AdminRolesBuilderTest extends TestCase
{
    private $securityHandler;
    private $authorizationChecker;
    private $admin;
    private $tokenStorage;
    private $token;
    private $pool;
    private $translator;

    private $securityInformation = [
        'GUEST' => [0 => 'VIEW', 1 => 'LIST'],
        'STAFF' => [0 => 'EDIT', 1 => 'LIST', 2 => 'CREATE'],
        'EDITOR' => [0 => 'OPERATOR', 1 => 'EXPORT'],
        'ADMIN' => [0 => 'MASTER'],
    ];

    protected function setUp(): void
    {
        $this->securityHandler = $this->createMock(SecurityHandlerInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->admin = $this->createMock(AdminInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->pool = $this->createMock(Pool::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
    }

    public function testGetPermissionLabels(): void
    {
        $this->translator->method('trans');

        $this->securityHandler->method('getBaseRole')
            ->willReturn('ROLE_SONATA_FOO_%s');

        $this->admin->method('getSecurityHandler')
            ->willReturn($this->securityHandler);

        $this->admin->method('getTranslator')
            ->willReturn($this->translator);

        $this->admin->method('getSecurityInformation')
            ->willReturn($this->securityInformation);

        $this->pool->expects($this->once())
            ->method('getAdminServiceIds')
            ->willReturn(['sonata.admin.bar']);

        $this->pool->expects($this->once())
            ->method('getInstance')
            ->with('sonata.admin.bar')
            ->willReturn($this->admin);

        $rolesBuilder = new AdminRolesBuilder(
            $this->authorizationChecker,
            $this->pool,
            $this->translator
        );

        $expected = [
            'GUEST' => 'GUEST',
            'STAFF' => 'STAFF',
            'EDITOR' => 'EDITOR',
            'ADMIN' => 'ADMIN',
        ];

        $this->assertSame($expected, $rolesBuilder->getPermissionLabels());
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

        $this->pool->expects($this->once())
            ->method('getAdminServiceIds')
            ->willReturn(['sonata.admin.bar']);

        $this->pool->expects($this->once())
            ->method('getInstance')
            ->with('sonata.admin.bar')
            ->willReturn($this->admin);

        $rolesBuilder = new AdminRolesBuilder(
            $this->authorizationChecker,
            $this->pool,
            $this->translator
        );

        $expected = [
            'ROLE_SONATA_FOO_GUEST' => [
                'role' => 'ROLE_SONATA_FOO_GUEST',
                'label' => 'GUEST',
                'role_translated' => 'ROLE_SONATA_FOO_GUEST',
                'is_granted' => false,
                'admin_label' => 'Foo',
            ],
            'ROLE_SONATA_FOO_STAFF' => [
                'role' => 'ROLE_SONATA_FOO_STAFF',
                'label' => 'STAFF',
                'role_translated' => 'ROLE_SONATA_FOO_STAFF',
                'is_granted' => false,
                'admin_label' => 'Foo',
            ],
            'ROLE_SONATA_FOO_EDITOR' => [
                'role' => 'ROLE_SONATA_FOO_EDITOR',
                'label' => 'EDITOR',
                'role_translated' => 'ROLE_SONATA_FOO_EDITOR',
                'is_granted' => false,
                'admin_label' => 'Foo',
            ],
            'ROLE_SONATA_FOO_ADMIN' => [
                'role' => 'ROLE_SONATA_FOO_ADMIN',
                'label' => 'ADMIN',
                'role_translated' => 'ROLE_SONATA_FOO_ADMIN',
                'is_granted' => false,
                'admin_label' => 'Foo',
            ],
        ];

        $this->assertSame($expected, $rolesBuilder->getRoles());
    }

    public function testGetAddExcludeAdmins(): void
    {
        $rolesBuilder = new AdminRolesBuilder(
            $this->authorizationChecker,
            $this->pool,
            $this->translator
        );
        $rolesBuilder->addExcludeAdmin('sonata.admin.bar');

        $this->assertSame(['sonata.admin.bar'], $rolesBuilder->getExcludeAdmins());
    }
}
