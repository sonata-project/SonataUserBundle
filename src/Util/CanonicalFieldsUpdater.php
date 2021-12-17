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

namespace Sonata\UserBundle\Util;

use Sonata\UserBundle\Model\UserInterface;

final class CanonicalFieldsUpdater implements CanonicalFieldsUpdaterInterface
{
    public function updateCanonicalFields(UserInterface $user): void
    {
        $user->setUsernameCanonical($this->canonicalizeUsername($user->getUsername()));
        $user->setEmailCanonical($this->canonicalizeEmail($user->getEmail()));
    }

    public function canonicalizeEmail(?string $email): ?string
    {
        return $this->canonicalize($email);
    }

    public function canonicalizeUsername(?string $username): ?string
    {
        return $this->canonicalize($username);
    }

    public function canonicalize(?string $string): ?string
    {
        if (null === $string) {
            return null;
        }

        $encoding = mb_detect_encoding($string, mb_detect_order(), true);

        return null !== $encoding
            ? mb_convert_case($string, \MB_CASE_LOWER, $encoding)
            : mb_convert_case($string, \MB_CASE_LOWER);
    }
}
