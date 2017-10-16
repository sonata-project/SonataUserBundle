<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Tests\Admin\Model;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Admin\Model\GroupAdmin;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class GroupAdminTest extends TestCase
{
    public function testInstance()
    {
        $admin = new GroupAdmin('admin.group', 'Sonata\UserBundle\Model\Group', 'SonataAdminBundle:CRUD');

        $this->assertNotEmpty($admin);
    }
}
