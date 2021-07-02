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

abstract class GroupManager implements GroupManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function createGroup($name)
    {
        $class = $this->getClass();

        return new $class($name);
    }

    /**
     * {@inheritdoc}
     */
    public function findGroupByName($name)
    {
        return $this->findGroupBy(['name' => $name]);
    }
}
