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
use FOS\UserBundle\Util\CanonicalFieldsUpdater;
use FOS\UserBundle\Util\PasswordUpdaterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Entity\BaseUser;
use Sonata\UserBundle\Entity\UserManager;

final class UserManagerTest extends TestCase
{
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

    private function getUserManager($qbCallback): UserManager
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
            'username',
            'email',
        ]);
        $metadata->method('getName')->willReturn('className');

        $om = $this->createMock(EntityManager::class);
        $om->method('getRepository')->willReturn($repository);
        $om->method('getClassMetadata')->willReturn($metadata);

        $passwordUpdater = $this->createMock(PasswordUpdaterInterface::class);
        $canonical = $this->createMock(CanonicalFieldsUpdater::class);

        return new UserManager($passwordUpdater, $canonical, $om, BaseUser::class);
    }
}
