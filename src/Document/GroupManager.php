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

use Sonata\UserBundle\Model\GroupManager as BaseGroupManager;
use Sonata\DatagridBundle\Pager\PagerInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class GroupManager extends BaseGroupManager
{
    /**
     * {@inheritdoc}
     */
    public function findGroupsBy(?array $criteria = null, ?array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, int $page, int $limit = 10, array $sort = []): PagerInterface
    {
        new \RuntimeException('method not implemented');
    }
}
