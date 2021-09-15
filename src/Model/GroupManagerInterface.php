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
     * @return array<int, GroupInterface>
     */
    public function findGroupsBy(
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array;

    /**
     * Returns an empty group instance.
     */
    public function createGroup(string $name): GroupInterface;

    /**
     * Deletes a group.
     */
    public function deleteGroup(GroupInterface $group): void;

    /**
     * Finds one group by the given criteria.
     */
    public function findGroupBy(array $criteria): ?GroupInterface;

    /**
     * Finds a group by name.
     */
    public function findGroupByName(string $name): ?GroupInterface;

    /**
     * Returns a collection with all group instances.
     *
     * @return array<int, GroupInterface>
     */
    public function findGroups(): array;

    /**
     * Returns the group's fully qualified class name.
     */
    public function getClass(): string;

    /**
     * Updates a group.
     */
    public function updateGroup(GroupInterface $group, bool $andFlush = true): void;
}
