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

interface GroupInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return GroupInterface
     */
    public function setName($name);

    /**
     * @return GroupInterface
     */
    public function getRoles();

    /**
     * @return GroupInterface
     */
    public function setRoles(array $roles);

    /**
     * @param string $role
     *
     * @return GroupInterface
     */
    public function addRole($role);

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role);

    /**
     * @param string $role
     *
     * @return GroupInterface
     */
    public function removeRole($role);
}
