<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Model;

use FOS\UserBundle\Model\Group as FOSGroup;

abstract class Group extends FOSGroup implements GroupInterface
{
    /**
     * Represents a string representation
     *
     * @return string
     */
    public function __toString ()
    {
        return $this->getName() ? : '';
    }
}
