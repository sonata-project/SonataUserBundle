<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Tests\Entity;

use Sonata\CoreBundle\Test\EntityManagerMockFactory;
use Sonata\UserBundle\Entity\GroupManager;

/**
 * Class GroupManagerTest.
 */
class GroupManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $qbCallback
     *
     * @return GroupManager
     */
    protected function getUserManager($qbCallback)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, [
            'name',
            'roles',
        ]);

        return new GroupManager($em, 'Sonata\UserBundle\Entity\BaseGroup');
    }

    public function testGetPager()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('g.name'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([], 1);
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Invalid sort field 'invalid' in 'className' class
     */
    public function testGetPagerWithInvalidSort()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->never())->method('orderBy');
                $qb->expects($self->never())->method('setParameters');
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithEnabledUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('g.enabled = :enabled'));
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('g.name'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['enabled' => true]));
            })
            ->getPager(['enabled' => true], 1);
    }

    public function testGetPagerWithDisabledUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('g.enabled = :enabled'));
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('g.name'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['enabled' => false]));
            })
            ->getPager(['enabled' => false], 1);
    }
}
