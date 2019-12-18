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
        $self = $this;
        $this
            ->getUserManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->willReturn(['g']);
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('g.name'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithInvalidSort(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid sort field \'invalid\' in \'className\' class');

        $self = $this;
        $this
            ->getUserManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->never())->method('orderBy');
                $qb->expects($self->never())->method('setParameters');
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithEnabledUsers(): void
    {
        $self = $this;
        $this
            ->getUserManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->willReturn(['g']);
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('g.enabled = :enabled'));
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('g.name'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['enabled' => true]));
            })
            ->getPager(['enabled' => true], 1);
    }

    public function testGetPagerWithDisabledUsers(): void
    {
        $self = $this;
        $this
            ->getUserManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->willReturn(['g']);
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('g.enabled = :enabled'));
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('g.name'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['enabled' => false]));
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
