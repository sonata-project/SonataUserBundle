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

namespace Sonata\UserBundle\Tests\Entity;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Entity\UserManager;
use Sonata\UserBundle\Entity\UserManagerProxy;

class UserManagerProxyTest extends TestCase
{
    public function testProxy(): void
    {
        $doctrine = $this->createStub(ManagerRegistry::class);

        $userManager = $this->getMockBuilder(UserManager::class)->disableOriginalConstructor()->getMock();

        $userManagerProxy = new UserManagerProxy('stClass', $doctrine, $userManager);

        $userManager->expects($this->once())->method('getClass');
        $userManagerProxy->getClass();

        $userManager->expects($this->once())->method('findAll');
        $userManagerProxy->findAll();

        $userManager->expects($this->once())->method('findBy');
        $userManagerProxy->findBy([]);

        $userManager->expects($this->once())->method('findOneBy');
        $userManagerProxy->findOneBy([]);

        $userManager->expects($this->once())->method('find');
        $userManagerProxy->find(10);

        $userManager->expects($this->once())->method('create');
        $userManagerProxy->create();

        $userManager->expects($this->once())->method('save');
        $userManagerProxy->save('grou');

        $userManager->expects($this->once())->method('delete');
        $userManagerProxy->delete('grou');

        $userManager->expects($this->once())->method('getTableName');
        $userManagerProxy->getTableName();

        $userManager->expects($this->once())->method('getConnection');
        $userManagerProxy->getConnection();
    }
}
