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

use FOS\UserBundle\Util\CanonicalFieldsUpdater;
use FOS\UserBundle\Util\PasswordUpdaterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Test\EntityManagerMockFactoryTrait;
use Sonata\UserBundle\Entity\BaseUser;
use Sonata\UserBundle\Entity\UserManager;

class UserManagerTest extends TestCase
{
    use EntityManagerMockFactoryTrait;

    public function testGetPager(): void
    {
        $this
            ->getUserManager(function (MockObject $qb): void {
                $qb->expects($this->once())->method('getRootAliases')->willReturn(['u']);
                $qb->expects($this->never())->method('andWhere');
                $qb->expects($this->once())->method('orderBy')->with(
                    $this->equalTo('u.username'),
                    $this->equalTo('ASC')
                );
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithInvalidSort(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid sort field \'invalid\' in \'Sonata\UserBundle\Entity\BaseUser\' class');

        $this
            ->getUserManager(function (MockObject $qb): void {
                $qb->expects($this->never())->method('andWhere');
                $qb->expects($this->never())->method('orderBy');
                $qb->expects($this->never())->method('setParameters');
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithValidSortDesc(): void
    {
        $this
            ->getUserManager(function (MockObject $qb): void {
                $qb->expects($this->once())->method('getRootAliases')->willReturn(['u']);
                $qb->expects($this->once())->method('andWhere')->with($this->equalTo('u.enabled = :enabled'));
                $qb->expects($this->once())->method('setParameter')->with(
                    $this->equalTo('enabled'),
                    $this->equalTo(true)
                );
                $qb->expects($this->once())->method('orderBy')->with(
                    $this->equalTo('u.email'),
                    $this->equalTo('DESC')
                );
            })
            ->getPager(['enabled' => true], 1, 10, ['email' => 'DESC']);
    }

    public function testGetPagerWithEnabledUsers(): void
    {
        $this
            ->getUserManager(function (MockObject $qb): void {
                $qb->expects($this->once())->method('getRootAliases')->willReturn(['u']);
                $qb->expects($this->once())->method('andWhere')->with($this->equalTo('u.enabled = :enabled'));
                $qb->expects($this->once())->method('setParameter')->with(
                    $this->equalTo('enabled'),
                    $this->equalTo(true)
                );
                $qb->expects($this->once())->method('orderBy')->with(
                    $this->equalTo('u.username'),
                    $this->equalTo('ASC')
                );
            })
            ->getPager(['enabled' => true], 1);
    }

    public function testGetPagerWithDisabledUsers(): void
    {
        $this
            ->getUserManager(function (MockObject $qb): void {
                $qb->expects($this->once())->method('getRootAliases')->willReturn(['u']);
                $qb->expects($this->once())->method('andWhere')->with($this->equalTo('u.enabled = :enabled'));
                $qb->expects($this->once())->method('setParameter')->with(
                    $this->equalTo('enabled'),
                    $this->equalTo(false)
                );
                $qb->expects($this->once())->method('orderBy')->with(
                    $this->equalTo('u.username'),
                    $this->equalTo('ASC')
                );
            })
            ->getPager(['enabled' => false], 1);
    }

    protected function getUserManager(\Closure $qbCallback): UserManager
    {
        $om = $this->createEntityManagerMock($qbCallback, [
            'username',
            'email',
        ]);

        $passwordUpdater = $this->createMock(PasswordUpdaterInterface::class);
        $canonical = $this->createMock(CanonicalFieldsUpdater::class);

        return new UserManager($passwordUpdater, $canonical, $om, BaseUser::class);
    }
}
