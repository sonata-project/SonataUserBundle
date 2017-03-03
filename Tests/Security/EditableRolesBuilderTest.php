<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Tests\Security\Authorization\Voter;

use Sonata\UserBundle\Security\EditableRolesBuilder;
use Sonata\UserBundle\Tests\Helpers\PHPUnit_Framework_TestCase;

class EditableRolesBuilderTest extends PHPUnit_Framework_TestCase
{
    public function getTokenStorageMock()
    {
        // Set the SecurityContext for Symfony <2.6
        // NEXT_MAJOR: Remove conditional return when bumping requirements to SF 2.6+
        if (interface_exists('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')) {
            return $this->createMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        }

        return $this->createMock('Symfony\Component\Security\Core\SecurityContextInterface');
    }

    public function getAuthorizationCheckerMock()
    {
        // Set the SecurityContext for Symfony <2.6
        // NEXT_MAJOR: Remove conditional return when bumping requirements to SF 2.6+
        if (interface_exists('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')) {
            return $this->createMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        }

        return $this->createMock('Symfony\Component\Security\Core\SecurityContextInterface');
    }

    /**
     * @group legacy
     */
    public function testRolesFromHierarchy()
    {
        $token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $tokenStorage = $this->getTokenStorageMock();
        $tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue($token));

        $authorizationChecker = $this->getAuthorizationCheckerMock();
        $authorizationChecker->expects($this->any())->method('isGranted')->will($this->returnValue(true));

        $pool = $this->getMockBuilder('Sonata\AdminBundle\Admin\Pool')
                ->disableOriginalConstructor()
                ->getMock();

        $pool->expects($this->once())->method('getAdminServiceIds')->will($this->returnValue(array()));

        $rolesHierarchy = array(
            'ROLE_ADMIN' => array(
                0 => 'ROLE_USER',
            ),
            'ROLE_SUPER_ADMIN' => array(
                0 => 'ROLE_USER',
                1 => 'ROLE_SONATA_ADMIN',
                2 => 'ROLE_ADMIN',
                3 => 'ROLE_ALLOWED_TO_SWITCH',
                4 => 'ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT',
                5 => 'ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT',
            ),
            'SONATA' => array(),
        );

        $expected = array(
            'ROLE_ADMIN' => 'ROLE_ADMIN: ROLE_USER',
            'ROLE_USER' => 'ROLE_USER',
            'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN: ROLE_USER, ROLE_SONATA_ADMIN, ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH, ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT, ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT',
            'ROLE_SONATA_ADMIN' => 'ROLE_SONATA_ADMIN',
            'ROLE_ALLOWED_TO_SWITCH' => 'ROLE_ALLOWED_TO_SWITCH',
            'ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT' => 'ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT',
            'ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT' => 'ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT',
            'SONATA' => 'SONATA: ',
        );

        $builder = new EditableRolesBuilder($tokenStorage, $authorizationChecker, $pool, $rolesHierarchy);
        list($roles, $rolesReadOnly) = $builder->getRoles();

        $this->assertEmpty($rolesReadOnly);
        $this->assertEquals($expected, $roles);
    }

    public function testRolesFromAdminWithMasterAdmin()
    {
        $securityHandler = $this->createMock('Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface');
        $securityHandler->expects($this->once())->method('getBaseRole')->will($this->returnValue('ROLE_FOO_%s'));

        $admin = $this->createMock('Sonata\AdminBundle\Admin\AdminInterface');
        $admin->expects($this->once())->method('isGranted')->will($this->returnValue(true));
        $admin->expects($this->once())->method('getSecurityInformation')->will($this->returnValue(array('GUEST' => array(0 => 'VIEW', 1 => 'LIST'), 'STAFF' => array(0 => 'EDIT', 1 => 'LIST', 2 => 'CREATE'), 'EDITOR' => array(0 => 'OPERATOR', 1 => 'EXPORT'), 'ADMIN' => array(0 => 'MASTER'))));
        $admin->expects($this->once())->method('getSecurityHandler')->will($this->returnValue($securityHandler));

        $token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $tokenStorage = $this->getTokenStorageMock();
        $tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue($token));

        $authorizationChecker = $this->getAuthorizationCheckerMock();
        $authorizationChecker->expects($this->any())->method('isGranted')->will($this->returnValue(true));

        $pool = $this->getMockBuilder('Sonata\AdminBundle\Admin\Pool')
                ->disableOriginalConstructor()
                ->getMock();

        $pool->expects($this->once())->method('getInstance')->will($this->returnValue($admin));
        $pool->expects($this->once())->method('getAdminServiceIds')->will($this->returnValue(array('myadmin')));

        $builder = new EditableRolesBuilder($tokenStorage, $authorizationChecker, $pool, array());

        $expected = array(
          'ROLE_FOO_GUEST' => 'ROLE_FOO_GUEST',
          'ROLE_FOO_STAFF' => 'ROLE_FOO_STAFF',
          'ROLE_FOO_EDITOR' => 'ROLE_FOO_EDITOR',
          'ROLE_FOO_ADMIN' => 'ROLE_FOO_ADMIN',
        );

        list($roles, $rolesReadOnly) = $builder->getRoles();
        $this->assertEmpty($rolesReadOnly);
        $this->assertEquals($expected, $roles);
    }

    public function testWithNoSecurityToken()
    {
        $tokenStorage = $this->getTokenStorageMock();
        $tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(null));

        $authorizationChecker = $this->getAuthorizationCheckerMock();
        $authorizationChecker->expects($this->any())->method('isGranted')->will($this->returnValue(false));

        $pool = $this->getMockBuilder('Sonata\AdminBundle\Admin\Pool')
                ->disableOriginalConstructor()
                ->getMock();

        $builder = new EditableRolesBuilder($tokenStorage, $authorizationChecker, $pool, array());

        list($roles, $rolesReadOnly) = $builder->getRoles();

        $this->assertEmpty($roles);
        $this->assertEmpty($rolesReadOnly);
    }
}
