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
use Sonata\UserBundle\Entity\UserManager;
use Sonata\UserBundle\Tests\Helpers\PHPUnit_Framework_TestCase;

class UserManagerTest extends PHPUnit_Framework_TestCase
{
    public function testGetPager()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(array('u')));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
            })
            ->getPager(array(), 1);
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Invalid sort field 'invalid' in 'Sonata\UserBundle\Entity\BaseUser' class
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

    public function testGetPagerWithValidSortDesc()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(array('u')));
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
            ->getPager(array('enabled' => true), 1, 10, array('email' => 'DESC'));
    }

    public function testGetPagerWithEnabledUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(array('u')));
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
            ->getPager(array('enabled' => true), 1);
    }

    public function testGetPagerWithDisabledUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(array('u')));
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
            ->getPager(array('enabled' => false), 1);
    }

    protected function getUserManager($qbCallback)
    {
        $om = EntityManagerMockFactory::create($this, $qbCallback, array(
            'username',
            'email',
        ));

        $passwordUpdater = $this->createMock('FOS\UserBundle\Util\PasswordUpdaterInterface');
        $canonical = $this->createMock('FOS\UserBundle\Util\CanonicalFieldsUpdater');

        return new UserManager($passwordUpdater, $canonical, $om, 'Sonata\UserBundle\Entity\BaseUser');
    }
}
