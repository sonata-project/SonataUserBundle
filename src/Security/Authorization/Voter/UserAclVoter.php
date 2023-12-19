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

namespace Sonata\UserBundle\Security\Authorization\Voter;

use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Acl\Voter\AclVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class UserAclVoter extends AclVoter
{
    public function supportsClass($class): bool
    {
        // support the Object-Scope ACL
        return is_subclass_of($class, UserInterface::class);
    }

    /**
     * @param mixed $attribute
     */
    public function supportsAttribute($attribute): bool
    {
        return 'EDIT' === $attribute || 'DELETE' === $attribute;
    }

    /**
     * @param mixed   $subject
     * @param mixed[] $attributes
     *
     * @return self::ACCESS_ABSTAIN|self::ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $subject, array $attributes): int
    {
        if (!\is_object($subject) || !$this->supportsClass($subject::class)) {
            return self::ACCESS_ABSTAIN;
        }

        foreach ($attributes as $attribute) {
            $tokenUser = $token->getUser();

            if ($this->supportsAttribute($attribute) && $subject instanceof UserInterface && $tokenUser instanceof UserInterface) {
                if ($subject->isSuperAdmin() && !$tokenUser->isSuperAdmin()) {
                    // deny a non super admin user to edit or delete a super admin user
                    return self::ACCESS_DENIED;
                }
            }
        }

        // leave the permission voting to the AclVoter that is using the default permission map
        return self::ACCESS_ABSTAIN;
    }
}
