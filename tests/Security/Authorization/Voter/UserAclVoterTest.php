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

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Security\Authorization\Voter\UserAclVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserAclVoterTest extends TestCase
{
    public function testVoteWillAbstainWhenAUserIsLoggedInAndASuperAdmin()
    {
        // Given
        $user = $this->createMock('FOS\UserBundle\Model\UserInterface');
        $user->expects($this->any())->method('isSuperAdmin')->will($this->returnValue(true));

        $loggedInUser = $this->createMock('FOS\UserBundle\Model\UserInterface');
        $loggedInUser->expects($this->any())->method('isSuperAdmin')->will($this->returnValue(true));

        $token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())->method('getUser')->will($this->returnValue($loggedInUser));

        $aclProvider = $this->createMock('Symfony\Component\Security\Acl\Model\AclProviderInterface');
        $oidRetrievalStrategy = $this->createMock('Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface');
        $sidRetrievalStrategy = $this->createMock('Symfony\Component\Security\Acl\Model\SecurityIdentityRetrievalStrategyInterface');
        $permissionMap = $this->createMock('Symfony\Component\Security\Acl\Permission\PermissionMapInterface');

        $voter = new UserAclVoter($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap);

        // When
        $decision = $voter->vote($token, $user, ['EDIT']);

        // Then
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $decision, 'Should abstain from voting');
    }

    public function testVoteWillDenyAccessWhenAUserIsLoggedInAndNotASuperAdmin()
    {
        // Given
        $user = $this->createMock('FOS\UserBundle\Model\UserInterface');
        $user->expects($this->any())->method('isSuperAdmin')->will($this->returnValue(true));

        $loggedInUser = $this->createMock('FOS\UserBundle\Model\UserInterface');
        $loggedInUser->expects($this->any())->method('isSuperAdmin')->will($this->returnValue(false));

        $token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())->method('getUser')->will($this->returnValue($loggedInUser));

        $aclProvider = $this->createMock('Symfony\Component\Security\Acl\Model\AclProviderInterface');
        $oidRetrievalStrategy = $this->createMock('Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface');
        $sidRetrievalStrategy = $this->createMock('Symfony\Component\Security\Acl\Model\SecurityIdentityRetrievalStrategyInterface');
        $permissionMap = $this->createMock('Symfony\Component\Security\Acl\Permission\PermissionMapInterface');

        $voter = new UserAclVoter($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap);

        // When
        $decision = $voter->vote($token, $user, ['EDIT']);

        // Then
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $decision, 'Should deny access');
    }

    public function testVoteWillAbstainWhenAUserIsNotAvailable()
    {
        // Given
        $user = $this->createMock('FOS\UserBundle\Model\UserInterface');
        $user->expects($this->any())->method('isSuperAdmin')->will($this->returnValue(true));

        $loggedInUser = null;

        $token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())->method('getUser')->will($this->returnValue($loggedInUser));

        $aclProvider = $this->createMock('Symfony\Component\Security\Acl\Model\AclProviderInterface');
        $oidRetrievalStrategy = $this->createMock('Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface');
        $sidRetrievalStrategy = $this->createMock('Symfony\Component\Security\Acl\Model\SecurityIdentityRetrievalStrategyInterface');
        $permissionMap = $this->createMock('Symfony\Component\Security\Acl\Permission\PermissionMapInterface');

        $voter = new UserAclVoter($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap);

        // When
        $decision = $voter->vote($token, $user, ['EDIT']);

        // Then
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $decision, 'Should abstain from voting');
    }

    public function testVoteWillAbstainWhenAUserIsLoggedInButIsNotAFOSUser()
    {
        // Given
        $user = $this->createMock('FOS\UserBundle\Model\UserInterface');
        $user->expects($this->any())->method('isSuperAdmin')->will($this->returnValue(true));

        $loggedInUser = $this->createMock(UserInterface::class);

        $token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())->method('getUser')->will($this->returnValue($loggedInUser));

        $aclProvider = $this->createMock('Symfony\Component\Security\Acl\Model\AclProviderInterface');
        $oidRetrievalStrategy = $this->createMock('Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface');
        $sidRetrievalStrategy = $this->createMock('Symfony\Component\Security\Acl\Model\SecurityIdentityRetrievalStrategyInterface');
        $permissionMap = $this->createMock('Symfony\Component\Security\Acl\Permission\PermissionMapInterface');

        $voter = new UserAclVoter($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap);

        // When
        $decision = $voter->vote($token, $user, ['EDIT']);

        // Then
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $decision, 'Should abstain from voting');
    }
}
