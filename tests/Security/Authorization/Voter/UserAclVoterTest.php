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

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Security\Authorization\Voter\UserAclVoter;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface as SonataUserInterface;

final class UserAclVoterTest extends TestCase
{
    public function testVoteWillAbstainWhenAUserIsLoggedInAndASuperAdmin(): void
    {
        // Given
        $user = $this->createMock(UserInterface::class);
        $user->method('isSuperAdmin')->willReturn(true);

        $loggedInUser = $this->createMock(UserInterface::class);
        $loggedInUser->method('isSuperAdmin')->willReturn(true);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($loggedInUser);

        $aclProvider = $this->createMock(AclProviderInterface::class);
        $oidRetrievalStrategy = $this->createMock(ObjectIdentityRetrievalStrategyInterface::class);
        $sidRetrievalStrategy = $this->createMock(SecurityIdentityRetrievalStrategyInterface::class);
        $permissionMap = $this->createMock(PermissionMapInterface::class);

        $voter = new UserAclVoter($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap);

        // When
        $decision = $voter->vote($token, $user, ['EDIT']);

        // Then
        static::assertSame(VoterInterface::ACCESS_ABSTAIN, $decision, 'Should abstain from voting');
    }

    public function testVoteWillDenyAccessWhenAUserIsLoggedInAndNotASuperAdmin(): void
    {
        // Given
        $user = $this->createMock(UserInterface::class);
        $user->method('isSuperAdmin')->willReturn(true);

        $loggedInUser = $this->createMock(UserInterface::class);
        $loggedInUser->method('isSuperAdmin')->willReturn(false);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($loggedInUser);

        $aclProvider = $this->createMock(AclProviderInterface::class);
        $oidRetrievalStrategy = $this->createMock(ObjectIdentityRetrievalStrategyInterface::class);
        $sidRetrievalStrategy = $this->createMock(SecurityIdentityRetrievalStrategyInterface::class);
        $permissionMap = $this->createMock(PermissionMapInterface::class);

        $voter = new UserAclVoter($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap);

        // When
        $decision = $voter->vote($token, $user, ['EDIT']);

        // Then
        static::assertSame(VoterInterface::ACCESS_DENIED, $decision, 'Should deny access');
    }

    public function testVoteWillAbstainWhenAUserIsNotAvailable(): void
    {
        // Given
        $user = $this->createMock(UserInterface::class);
        $user->method('isSuperAdmin')->willReturn(true);

        $loggedInUser = null;

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($loggedInUser);

        $aclProvider = $this->createMock(AclProviderInterface::class);
        $oidRetrievalStrategy = $this->createMock(ObjectIdentityRetrievalStrategyInterface::class);
        $sidRetrievalStrategy = $this->createMock(SecurityIdentityRetrievalStrategyInterface::class);
        $permissionMap = $this->createMock(PermissionMapInterface::class);

        $voter = new UserAclVoter($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap);

        // When
        $decision = $voter->vote($token, $user, ['EDIT']);

        // Then
        static::assertSame(VoterInterface::ACCESS_ABSTAIN, $decision, 'Should abstain from voting');
    }

    public function testVoteWillAbstainWhenAUserIsLoggedInButIsNotASonataUser(): void
    {
        // Given
        $user = $this->createMock(UserInterface::class);
        $user->method('isSuperAdmin')->willReturn(true);

        $loggedInUser = $this->createMock(SonataUserInterface::class);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($loggedInUser);

        $aclProvider = $this->createMock(AclProviderInterface::class);
        $oidRetrievalStrategy = $this->createMock(ObjectIdentityRetrievalStrategyInterface::class);
        $sidRetrievalStrategy = $this->createMock(SecurityIdentityRetrievalStrategyInterface::class);
        $permissionMap = $this->createMock(PermissionMapInterface::class);

        $voter = new UserAclVoter($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap);

        // When
        $decision = $voter->vote($token, $user, ['EDIT']);

        // Then
        static::assertSame(VoterInterface::ACCESS_ABSTAIN, $decision, 'Should abstain from voting');
    }
}
