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

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
interface GroupableInterface
{
    /**
     * Gets the groups granted to the user.
     *
     * @return \Traversable
     */
    public function getGroups();

    /**
     * Gets the name of the groups which includes the user.
     *
     * @return array
     */
    public function getGroupNames();

    /**
     * Indicates whether the user belongs to the specified group or not.
     *
     * @param string $name Name of the group
     *
     * @return bool
     */
    public function hasGroup($name);

    /**
     * Add a group to the user groups.
     *
     * @return static
     */
    public function addGroup(GroupInterface $group);

    /**
     * Remove a group from the user groups.
     *
     * @return static
     */
    public function removeGroup(GroupInterface $group);
}
