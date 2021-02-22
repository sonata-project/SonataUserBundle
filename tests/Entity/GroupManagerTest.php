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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use Sonata\Doctrine\Test\EntityManagerMockFactoryTrait;
use Sonata\UserBundle\Entity\BaseGroup;
use Sonata\UserBundle\Entity\GroupManager;

class GroupManagerTest extends TestCase
{
    use EntityManagerMockFactoryTrait;

    public function testGetPager(): void
    {
        $this
            ->getUserManager(function (MockObject $qb): void {
                $qb->expects($this->once())->method('getRootAliases')->willReturn(['g']);
                $qb->expects($this->never())->method('andWhere');
                $qb->expects($this->once())->method('orderBy')->with(
                    $this->equalTo('g.name'),
                    $this->equalTo('ASC')
                );
                $qb->expects($this->once())->method('setParameters')->with($this->equalTo([]));
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithInvalidSort(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid sort field \'invalid\' in \'className\' class');

        $this
            ->getUserManager(function (MockObject $qb): void {
                $qb->expects($this->never())->method('andWhere');
                $qb->expects($this->never())->method('orderBy');
                $qb->expects($this->never())->method('setParameters');
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithEnabledUsers(): void
    {
        $this
            ->getUserManager(function (MockObject $qb): void {
                $qb->expects($this->once())->method('getRootAliases')->willReturn(['g']);
                $qb->expects($this->once())->method('andWhere')->with($this->equalTo('g.enabled = :enabled'));
                $qb->expects($this->once())->method('orderBy')->with(
                    $this->equalTo('g.name'),
                    $this->equalTo('ASC')
                );
                $qb->expects($this->once())->method('setParameters')->with($this->equalTo(['enabled' => true]));
            })
            ->getPager(['enabled' => true], 1);
    }

    public function testGetPagerWithDisabledUsers(): void
    {
        $this
            ->getUserManager(function (MockObject $qb): void {
                $qb->expects($this->once())->method('getRootAliases')->willReturn(['g']);
                $qb->expects($this->once())->method('andWhere')->with($this->equalTo('g.enabled = :enabled'));
                $qb->expects($this->once())->method('orderBy')->with(
                    $this->equalTo('g.name'),
                    $this->equalTo('ASC')
                );
                $qb->expects($this->once())->method('setParameters')->with($this->equalTo(['enabled' => false]));
            })
            ->getPager(['enabled' => false], 1);
    }

    protected function getUserManager(\Closure $qbCallback): GroupManager
    {
        $em = $this->createEntityManagerMock($qbCallback, [
            'name',
            'roles',
        ]);

        return new GroupManager($em, BaseGroup::class);
    }
}
