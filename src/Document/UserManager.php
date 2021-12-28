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

namespace Sonata\UserBundle\Document;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\UserBundle\Model\UserManagerInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class UserManager extends BaseUserManager implements UserManagerInterface
{
    public function findUsersBy(?array $criteria = null, ?array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * NEXT_MAJOR: remove this method.
     *
     * @deprecated since sonata-project/user-bundle 4.14, to be removed in 5.0.
     */
    public function getPager(array $criteria, int $page, int $limit = 10, array $sort = []): PagerInterface
    {
        throw new \RuntimeException('method not implemented');
    }
}
