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
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
interface GroupInterface
{
    /**
     * @param string $role
     *
     * @return static
     */
    public function addRole($role);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role);

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param string $role
     *
     * @return static
     */
    public function removeRole($role);

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName($name);

    /**
     * @return static
     */
    public function setRoles(array $roles);
}
