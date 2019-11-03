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

use Sonata\UserBundle\Model\GroupInterface;
use Sonata\UserBundle\Model\FOSGroupManagerInterface as BaseInterface;
use Sonata\CoreBundle\Model\PageableManagerInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
interface GroupManagerInterface extends BaseInterface, PageableManagerInterface
{
    /**
     * Alias for the repository method.
     *
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return GroupInterface[]
     */
    public function findGroupsBy(array $criteria = null, array $orderBy = null, $limit = null, $offset = null);
}
