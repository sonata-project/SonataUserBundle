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

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Entity\BaseGroup;
use Sonata\UserBundle\Entity\GroupManager;

/**
 * NEXT_MAJOR: Remove this class.
 *
 * @group legacy
 */
final class GroupManagerTest extends TestCase
{
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

    private function getUserManager(\Closure $qbCallback): GroupManager
    {
        $query = $this->createMock(AbstractQuery::class);
        $query->method('execute')->willReturn(true);

        $qb = $this->createMock(QueryBuilder::class);

        $qb->method('select')->willReturn($qb);
        $qb->method('getQuery')->willReturn($query);
        $qb->method('where')->willReturn($qb);
        $qb->method('orderBy')->willReturn($qb);
        $qb->method('andWhere')->willReturn($qb);
        $qb->method('leftJoin')->willReturn($qb);

        $qbCallback($qb);

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('createQueryBuilder')->willReturn($qb);

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getFieldNames')->willReturn([
            'name',
            'roles',
        ]);
        $metadata->method('getName')->willReturn('className');

        $em = $this->createMock(EntityManager::class);
        $em->method('getRepository')->willReturn($repository);
        $em->method('getClassMetadata')->willReturn($metadata);

        return new GroupManager($em, BaseGroup::class);
    }
}
