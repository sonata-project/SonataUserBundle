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
use Sonata\UserBundle\Security\RolesBuilder\AdminRolesBuilderInterface;
use Sonata\UserBundle\Security\RolesBuilder\ExpandableRolesBuilderInterface;
use Sonata\UserBundle\Security\RolesBuilder\MatrixRolesBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class MatrixRolesBuilderTest extends TestCase
{
    private $tokenStorage;
    private $token;
    private $adminRolesBuilder;
    private $securityRolesBuilder;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->adminRolesBuilder = $this->createMock(AdminRolesBuilderInterface::class);
        $this->securityRolesBuilder = $this->createMock(ExpandableRolesBuilderInterface::class);
    }

    public function testGetPermissionLabels(): void
    {
        $expected = ['EDIT' => 'EDIT', 'LIST' => 'LIST', 'CREATE' => 'CREATE'];

        $this->adminRolesBuilder->method('getPermissionLabels')
            ->willReturn($expected);

        $matrixRolesBuilder = new MatrixRolesBuilder(
            $this->tokenStorage,
            $this->adminRolesBuilder,
            $this->securityRolesBuilder
        );

        $this->assertSame($expected, $matrixRolesBuilder->getPermissionLabels());
    }

    public function testGetRoles(): void
    {
        $this->tokenStorage->method('getToken')
            ->willReturn($this->token);

        $adminRoles = [
            'ROLE_SONATA_FOO_GUEST' => [
                'role' => 'ROLE_SONATA_FOO_GUEST',
                'label' => 'GUEST',
                'role_translated' => 'ROLE_SONATA_FOO_GUEST',
                'is_granted' => false,
                'admin_label' => 'Foo',
            ],
        ];

        $this->adminRolesBuilder->method('getRoles')
            ->willReturn($adminRoles);

        $securityRoles = [
            'ROLE_FOO' => [
                'role' => 'ROLE_FOO',
                'role_translated' => 'ROLE_FOO: ROLE_BAR, ROLE_ADMIN',
                'is_granted' => true,
            ],
        ];

        $this->securityRolesBuilder->method('getRoles')
            ->willReturn($securityRoles);

        $matrixRolesBuilder = new MatrixRolesBuilder(
            $this->tokenStorage,
            $this->adminRolesBuilder,
            $this->securityRolesBuilder
        );

        $expected = array_merge($securityRoles, $adminRoles);

        $this->assertSame($expected, $matrixRolesBuilder->getRoles());
    }

    public function testGetRolesNoToken(): void
    {
        $this->tokenStorage->method('getToken')
            ->willReturn(null);

        $matrixRolesBuilder = new MatrixRolesBuilder(
            $this->tokenStorage,
            $this->adminRolesBuilder,
            $this->securityRolesBuilder
        );

        $this->assertEmpty($matrixRolesBuilder->getRoles());
    }
}
