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

use Sonata\DatagridBundle\Pager\PageableInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
interface GroupManagerInterface extends PageableInterface
{
    /**
     * Alias for the repository method.
     *
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return GroupInterface[]
     */
    public function findGroupsBy(?array $criteria = null, ?array $orderBy = null, $limit = null, $offset = null);

    /**
     * Returns an empty group instance.
     *
     * @param string $name
     *
     * @return GroupInterface
     */
    public function createGroup($name);

    /**
     * Deletes a group.
     */
    public function deleteGroup(GroupInterface $group);

    /**
     * Finds one group by the given criteria.
     *
     * @return GroupInterface
     */
    public function findGroupBy(array $criteria);

    /**
     * Finds a group by name.
     *
     * @param string $name
     *
     * @return GroupInterface
     */
    public function findGroupByName($name);

    /**
     * Returns a collection with all group instances.
     *
     * @return \Traversable
     */
    public function findGroups();

    /**
     * Returns the group's fully qualified class name.
     *
     * @return string
     */
    public function getClass();

    /**
     * Updates a group.
     */
    public function updateGroup(GroupInterface $group);
}
