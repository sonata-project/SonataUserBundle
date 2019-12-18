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
        $self = $this;
        $this
            ->getUserManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->willReturn(['u']);
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithInvalidSort(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid sort field \'invalid\' in \'Sonata\UserBundle\Entity\BaseUser\' class');

        $self = $this;
        $this
            ->getUserManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->never())->method('orderBy');
                $qb->expects($self->never())->method('setParameters');
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithValidSortDesc(): void
    {
        $self = $this;
        $this
            ->getUserManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->willReturn(['u']);
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('u.enabled = :enabled'));
                $qb->expects($self->once())->method('setParameter')->with(
                    $self->equalTo('enabled'),
                    $self->equalTo(true)
                );
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.email'),
                    $self->equalTo('DESC')
                );
            })
            ->getPager(['enabled' => true], 1, 10, ['email' => 'DESC']);
    }

    public function testGetPagerWithEnabledUsers(): void
    {
        $self = $this;
        $this
            ->getUserManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->willReturn(['u']);
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('u.enabled = :enabled'));
                $qb->expects($self->once())->method('setParameter')->with(
                    $self->equalTo('enabled'),
                    $self->equalTo(true)
                );
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
            })
            ->getPager(['enabled' => true], 1);
    }

    public function testGetPagerWithDisabledUsers(): void
    {
        $self = $this;
        $this
            ->getUserManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->willReturn(['u']);
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('u.enabled = :enabled'));
                $qb->expects($self->once())->method('setParameter')->with(
                    $self->equalTo('enabled'),
                    $self->equalTo(false)
                );
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
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
