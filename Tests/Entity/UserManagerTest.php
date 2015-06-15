<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Tests\Entity;

use Sonata\CoreBundle\Test\EntityManagerMockFactory;
use Sonata\UserBundle\Entity\UserManager;

/**
 * Class UserManagerTest.
 */
class UserManagerTest extends \PHPUnit_Framework_TestCase
{
    protected function getUserManager($qbCallback)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, array(
            'username',
            'email',
        ));

        $encoder       = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $canonicalizer = $this->getMock('FOS\UserBundle\Util\CanonicalizerInterface');

        return new UserManager($encoder, $canonicalizer, $canonicalizer, $em, 'Sonata\UserBundle\Entity\BaseUser');
    }

    public function testGetPager()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array()));
            })
            ->getPager(array(), 1);
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
            ->getPager(array(), 1, 10, array('invalid' => 'ASC'));
    }

    public function testGetPagerWithEnabledUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('u.enabled = :enabled'));
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array('enabled' => true)));
            })
            ->getPager(array('enabled' => true), 1);
    }

    public function testGetPagerWithDisabledUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('u.enabled = :enabled'));
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array('enabled' => false)));
            })
            ->getPager(array('enabled' => false), 1);
    }

    public function testGetPagerWithLockedUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('u.locked = :locked'));
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array('locked' => true)));
            })
            ->getPager(array('locked' => true), 1);
    }

    public function testGetPagerWithNonLockedUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('u.locked = :locked'));
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array('locked' => false)));
            })
            ->getPager(array('locked' => false), 1);
    }
}
