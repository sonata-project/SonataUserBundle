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

namespace Sonata\UserBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface;
use Sonata\UserBundle\Security\RolesMatrixBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class RolesMatrixBuilderTest extends TestCase
{
    private $securityHandler;
    private $authorizationChecker;
    private $admin;
    private $tokenStorage;
    private $token;
    private $pool;
    private $translator;
    private $rolesHierarchy = ['ROLE_FOO' => ['ROLE_BAR', 'ROLE_ADMIN']];

    private $securityInformation = [
        'GUEST' => [0 => 'VIEW', 1 => 'LIST'],
        'STAFF' => [0 => 'EDIT', 1 => 'LIST', 2 => 'CREATE'],
        'EDITOR' => [0 => 'OPERATOR', 1 => 'EXPORT'],
        'ADMIN' => [0 => 'MASTER'],
    ];

    public function setUp(): void
    {
        $this->securityHandler = $this->createMock(SecurityHandlerInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->admin = $this->createMock(AdminInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->pool = $this->createMock(Pool::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
    }

    public function testGetRolesNoToken(): void
    {
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn([]);

        $rolesBuilder = new RolesMatrixBuilder(
            $this->tokenStorage,
            $this->authorizationChecker,
            $this->pool
        );
        $this->assertEmpty($rolesBuilder->getRoles());
    }

    public function testGetRoles(): void
    {
        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn(['tokenshouldbethere']);

        $this->pool->expects($this->any())
            ->method('getOption')
            ->willReturn('ROLE_FOO');

        $this->pool->expects($this->once())
            ->method('getAdminServiceIds')
            ->willReturn(['sonata.admin.foo']);

        $this->pool->expects($this->once())
            ->method('getInstance')
            ->with('sonata.admin.foo')
            ->willReturn($this->admin);

        $this->securityHandler->expects($this->once())
            ->method('getBaseRole')
            ->with($this->admin)
            ->willReturn('ROLE_BASE_FOO_%s');

        $this->admin->expects($this->once())
            ->method('getSecurityHandler')
            ->willReturn($this->securityHandler);

        $this->admin->expects($this->once())
            ->method('getSecurityInformation')
            ->willReturn($this->securityInformation);

        $this->admin->expects($this->atLeastOnce())
            ->method('getTranslator')
            ->willReturn($this->translator);

        $this->admin->expects($this->atLeastOnce())
            ->method('getLabel')
            ->willReturn('foo_admin_label');

        $this->translator->expects($this->atLeastOnce())
            ->method('trans')
            ->willReturn('translated foo admin');

        $rolesBuilder = new RolesMatrixBuilder(
            $this->tokenStorage,
            $this->authorizationChecker,
            $this->pool,
            $this->rolesHierarchy
        );

        $expected = [
            'ROLE_FOO' => [
                'role' => 'ROLE_FOO',
                'role_translated' => 'ROLE_FOO: ROLE_BAR, ROLE_ADMIN',
                'is_granted' => null,
            ],
            'ROLE_BAR' => [
                'role' => 'ROLE_BAR',
                'role_translated' => 'ROLE_BAR',
                'is_granted' => null,
            ],
            'ROLE_ADMIN' => [
                'role' => 'ROLE_ADMIN',
                'role_translated' => 'ROLE_ADMIN',
                'is_granted' => null,
            ],
            'ROLE_BASE_FOO_GUEST' => [
                'role' => 'ROLE_BASE_FOO_GUEST',
                'label' => 'GUEST',
                'role_translated' => 'ROLE_BASE_FOO_GUEST',
                'is_granted' => false,
                'admin_label' => 'translated foo admin',
            ],
            'ROLE_BASE_FOO_STAFF' => [
                'role' => 'ROLE_BASE_FOO_STAFF',
                'label' => 'STAFF',
                'role_translated' => 'ROLE_BASE_FOO_STAFF',
                'is_granted' => false,
                'admin_label' => 'translated foo admin',
            ],
            'ROLE_BASE_FOO_EDITOR' => [
                'role' => 'ROLE_BASE_FOO_EDITOR',
                'label' => 'EDITOR',
                'role_translated' => 'ROLE_BASE_FOO_EDITOR',
                'is_granted' => false,
                'admin_label' => 'translated foo admin',
            ],
            'ROLE_BASE_FOO_ADMIN' => [
                'role' => 'ROLE_BASE_FOO_ADMIN',
                'label' => 'ADMIN',
                'role_translated' => 'ROLE_BASE_FOO_ADMIN',
                'is_granted' => false,
                'admin_label' => 'translated foo admin',
            ],
        ];
        $this->assertSame($expected, $rolesBuilder->getRoles());
    }

    public function testGetAddExcludeAdmin(): void
    {
        $rolesBuilder = new RolesMatrixBuilder(
            $this->tokenStorage,
            $this->authorizationChecker,
            $this->pool
        );
        $rolesBuilder->addExcludeAdmin('sonata.admin.bar');

        $this->assertSame(['sonata.admin.bar'], $rolesBuilder->getExcludeAdmin());
    }
}
