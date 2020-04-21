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
use Sonata\UserBundle\Security\RolesBuilder\SecurityRolesBuilder;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class SecurityRolesBuilderTest extends TestCase
{
    private $authorizationChecker;
    private $admin;
    private $pool;
    private $translator;
    private $rolesHierarchy = ['ROLE_FOO' => ['ROLE_BAR', 'ROLE_ADMIN']];

    protected function setUp(): void
    {
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->admin = $this->createMock(AdminInterface::class);
        $this->pool = $this->createMock(Pool::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
    }

    public function testGetRoles(): void
    {
        $this->pool->expects($this->at(0))
            ->method('getOption')
            ->with('role_super_admin')
            ->willReturn('ROLE_SUPER_ADMIN');

        $this->pool->expects($this->at(1))
            ->method('getOption')
            ->with('role_admin')
            ->willReturn('ROLE_SONATA_ADMIN');

        $securityRolesBuilder = new SecurityRolesBuilder(
            $this->authorizationChecker,
            $this->pool,
            $this->translator,
            $this->rolesHierarchy
        );

        $this->authorizationChecker->method('isGranted')
            ->willReturn(true);

        $expected = [
            'ROLE_SUPER_ADMIN' => [
                'role' => 'ROLE_SUPER_ADMIN',
                'role_translated' => 'ROLE_SUPER_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_SONATA_ADMIN' => [
                'role' => 'ROLE_SONATA_ADMIN',
                'role_translated' => 'ROLE_SONATA_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_FOO' => [
                'role' => 'ROLE_FOO',
                'role_translated' => 'ROLE_FOO: ROLE_BAR, ROLE_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_BAR' => [
                'role' => 'ROLE_BAR',
                'role_translated' => 'ROLE_BAR',
                'is_granted' => true,
            ],
            'ROLE_ADMIN' => [
                'role' => 'ROLE_ADMIN',
                'role_translated' => 'ROLE_ADMIN',
                'is_granted' => true,
            ],
        ];

        $this->assertSame($expected, $securityRolesBuilder->getExpandedRoles());
    }

    public function testGetRolesNotExpanded(): void
    {
        $this->pool->expects($this->at(0))
            ->method('getOption')
            ->with('role_super_admin')
            ->willReturn('ROLE_SUPER_ADMIN');

        $this->pool->expects($this->at(1))
            ->method('getOption')
            ->with('role_admin')
            ->willReturn('ROLE_SONATA_ADMIN');

        $securityRolesBuilder = new SecurityRolesBuilder(
            $this->authorizationChecker,
            $this->pool,
            $this->translator,
            $this->rolesHierarchy
        );

        $this->authorizationChecker->method('isGranted')
            ->willReturn(true);

        $expected = [
            'ROLE_SUPER_ADMIN' => [
                'role' => 'ROLE_SUPER_ADMIN',
                'role_translated' => 'ROLE_SUPER_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_SONATA_ADMIN' => [
                'role' => 'ROLE_SONATA_ADMIN',
                'role_translated' => 'ROLE_SONATA_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_FOO' => [
                'role' => 'ROLE_FOO',
                'role_translated' => 'ROLE_FOO',
                'is_granted' => true,
            ],
            'ROLE_BAR' => [
                'role' => 'ROLE_BAR',
                'role_translated' => 'ROLE_BAR',
                'is_granted' => true,
            ],
            'ROLE_ADMIN' => [
                'role' => 'ROLE_ADMIN',
                'role_translated' => 'ROLE_ADMIN',
                'is_granted' => true,
            ],
        ];

        $this->assertSame($expected, $securityRolesBuilder->getRoles(null));
    }

    public function testGetRolesWithExistingRole(): void
    {
        $this->pool->expects($this->at(0))
            ->method('getOption')
            ->with('role_super_admin')
            ->willReturn('ROLE_SUPER_ADMIN');

        $this->pool->expects($this->at(1))
            ->method('getOption')
            ->with('role_admin')
            ->willReturn('ROLE_SONATA_ADMIN');

        $this->rolesHierarchy['ROLE_STAFF'] = ['ROLE_SUPER_ADMIN', 'ROLE_SUPER_ADMIN'];

        $securityRolesBuilder = new SecurityRolesBuilder(
            $this->authorizationChecker,
            $this->pool,
            $this->translator,
            $this->rolesHierarchy
        );

        $this->authorizationChecker->method('isGranted')
            ->willReturn(true);

        $expected = [
            'ROLE_SUPER_ADMIN' => [
                'role' => 'ROLE_SUPER_ADMIN',
                'role_translated' => 'ROLE_SUPER_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_SONATA_ADMIN' => [
                'role' => 'ROLE_SONATA_ADMIN',
                'role_translated' => 'ROLE_SONATA_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_FOO' => [
                'role' => 'ROLE_FOO',
                'role_translated' => 'ROLE_FOO: ROLE_BAR, ROLE_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_BAR' => [
                'role' => 'ROLE_BAR',
                'role_translated' => 'ROLE_BAR',
                'is_granted' => true,
            ],
            'ROLE_ADMIN' => [
                'role' => 'ROLE_ADMIN',
                'role_translated' => 'ROLE_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_STAFF' => [
                'role' => 'ROLE_STAFF',
                'role_translated' => 'ROLE_STAFF: ROLE_SUPER_ADMIN, ROLE_SUPER_ADMIN',
                'is_granted' => true,
            ],
        ];

        $this->assertSame($expected, $securityRolesBuilder->getExpandedRoles());
    }
}
