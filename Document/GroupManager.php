<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Document;

use FOS\UserBundle\Document\GroupManager as BaseGroupManager;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\UserBundle\Model\GroupManagerInterface;

/**
 * Class GroupManager.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class GroupManager extends BaseGroupManager implements GroupManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findGroupsBy(array $criteria = null, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @param int   $page
     * @param int   $limit
     * @param array $sort
     *
     * @return PagerInterface
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = array())
    {
        new \RuntimeException('method not implemented');
    }
}
