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

namespace Sonata\UserBundle\Model;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;

abstract class UserManager extends BaseUserManager implements UserManagerInterface
{
    /**
     * @return array<int, UserInterface>
     */
    public function findUsersBy(
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }
}
