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

namespace Sonata\UserBundle\Tests\Admin\Model;

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\UserBundle\Admin\Model\UserAdmin;
use Sonata\UserBundle\Model\User;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class UserAdminTest extends TestCase
{
    public function testInstance(): void
    {
        $admin = new UserAdmin('admin.group', User::class, CRUDController::class);

        $this->assertNotEmpty($admin);
    }
}
