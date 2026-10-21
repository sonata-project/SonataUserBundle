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

use PHPUnit\Framework\Attributes\IgnoreDeprecations;
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

/**
 * Not a real legacy test but a deprecation from Symfony is reported
 * as a self deprecation notices rather than an indirect one.
 */
#[IgnoreDeprecations]
final class UserAclVoterTest extends TestCase
{
    public function testVoteWillAbstainWhenAUserIsLoggedInAndASuperAdmin(): void
    {
        // Given
        $user = static::createStub(UserInterface::class);
        $user->method('isSuperAdmin')->willReturn(true);

        $loggedInUser = static::createStub(UserInterface::class);
        $loggedInUser->method('isSuperAdmin')->willReturn(true);

        $token = static::createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($loggedInUser);

        $aclProvider = static::createStub(AclProviderInterface::class);
        $oidRetrievalStrategy = static::createStub(ObjectIdentityRetrievalStrategyInterface::class);
        $sidRetrievalStrategy = static::createStub(SecurityIdentityRetrievalStrategyInterface::class);
        $permissionMap = static::createStub(PermissionMapInterface::class);

        $voter = new UserAclVoter($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap);

        // When
        $decision = $voter->vote($token, $user, ['EDIT']);

        // Then
        static::assertSame(VoterInterface::ACCESS_ABSTAIN, $decision, 'Should abstain from voting');
    }

    public function testVoteWillDenyAccessWhenAUserIsLoggedInAndNotASuperAdmin(): void
    {
        // Given
        $user = static::createStub(UserInterface::class);
        $user->method('isSuperAdmin')->willReturn(true);

        $loggedInUser = static::createStub(UserInterface::class);
        $loggedInUser->method('isSuperAdmin')->willReturn(false);

        $token = static::createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($loggedInUser);

        $aclProvider = static::createStub(AclProviderInterface::class);
        $oidRetrievalStrategy = static::createStub(ObjectIdentityRetrievalStrategyInterface::class);
        $sidRetrievalStrategy = static::createStub(SecurityIdentityRetrievalStrategyInterface::class);
        $permissionMap = static::createStub(PermissionMapInterface::class);

        $voter = new UserAclVoter($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap);

        // When
        $decision = $voter->vote($token, $user, ['EDIT']);

        // Then
        static::assertSame(VoterInterface::ACCESS_DENIED, $decision, 'Should deny access');
    }

    public function testVoteWillAbstainWhenAUserIsNotAvailable(): void
    {
        // Given
        $user = static::createStub(UserInterface::class);
        $user->method('isSuperAdmin')->willReturn(true);

        $loggedInUser = null;

        $token = static::createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($loggedInUser);

        $aclProvider = static::createStub(AclProviderInterface::class);
        $oidRetrievalStrategy = static::createStub(ObjectIdentityRetrievalStrategyInterface::class);
        $sidRetrievalStrategy = static::createStub(SecurityIdentityRetrievalStrategyInterface::class);
        $permissionMap = static::createStub(PermissionMapInterface::class);

        $voter = new UserAclVoter($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap);

        // When
        $decision = $voter->vote($token, $user, ['EDIT']);

        // Then
        static::assertSame(VoterInterface::ACCESS_ABSTAIN, $decision, 'Should abstain from voting');
    }

    public function testVoteWillAbstainWhenAUserIsLoggedInButIsNotASonataUser(): void
    {
        // Given
        $user = static::createStub(UserInterface::class);
        $user->method('isSuperAdmin')->willReturn(true);

        $loggedInUser = static::createStub(SonataUserInterface::class);

        $token = static::createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($loggedInUser);

        $aclProvider = static::createStub(AclProviderInterface::class);
        $oidRetrievalStrategy = static::createStub(ObjectIdentityRetrievalStrategyInterface::class);
        $sidRetrievalStrategy = static::createStub(SecurityIdentityRetrievalStrategyInterface::class);
        $permissionMap = static::createStub(PermissionMapInterface::class);

        $voter = new UserAclVoter($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap);

        // When
        $decision = $voter->vote($token, $user, ['EDIT']);

        // Then
        static::assertSame(VoterInterface::ACCESS_ABSTAIN, $decision, 'Should abstain from voting');
    }
}
