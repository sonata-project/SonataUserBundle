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

use PHPUnit\Framework\TestCase;
use Sonata\CoreBundle\Test\EntityManagerMockFactory;
use Sonata\UserBundle\Entity\UserManager;

class UserManagerTest extends TestCase
{
    public function testGetPager()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['u']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->never())->method('setParameter');
                $qb->expects($self->never())->method('setParameters');
            })
            ->getPager([], 1);
    }

    /**
     * @expectedException        \RuntimeException
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

    public function testGetPagerWithValidSortDesc()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['u']));
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

    public function testGetPagerWithEnabledUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['u']));
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

    public function testGetPagerWithDisabledUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['u']));
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

    public function testGetPagerWithLockedUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['u']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('u.locked = :locked'));
                $qb->expects($self->once())->method('setParameter')->with(
                    $self->equalTo('locked'),
                    $self->equalTo(true)
                );
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
            })
            ->getPager(['locked' => true], 1);
    }

    public function testGetPagerWithNonLockedUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['u']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('u.locked = :locked'));
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->any())->method('setParameter')->with(
                    $self->equalTo('locked'),
                    $self->equalTo(false)
                );
            })
            ->getPager(['locked' => false], 1);
    }

    public function testGetPagerWithDisabledAndNonLockedUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['u']));
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    [$self->equalTo('u.enabled = :enabled')],
                    [$self->equalTo('u.locked = :locked')]
                );
                $qb->expects($self->exactly(2))->method('setParameter')->withConsecutive(
                    [
                        $self->equalTo('enabled'),
                        $self->equalTo(false),
                    ],
                    [
                        $self->equalTo('locked'),
                        $self->equalTo(false),
                    ]
                );
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
            })
            ->getPager(['enabled' => false, 'locked' => false], 1);
    }

    public function testGetPagerWithEnabledAndNonLockedUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['u']));
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    [$self->equalTo('u.enabled = :enabled')],
                    [$self->equalTo('u.locked = :locked')]
                );
                $qb->expects($self->exactly(2))->method('setParameter')->withConsecutive(
                    [
                        $self->equalTo('enabled'),
                        $self->equalTo(true),
                    ],
                    [
                        $self->equalTo('locked'),
                        $self->equalTo(false),
                    ]
                );
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
            })
            ->getPager(['enabled' => true, 'locked' => false], 1);
    }

    public function testGetPagerWithEnabledAndLockedUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['u']));
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    [$self->equalTo('u.enabled = :enabled')],
                    [$self->equalTo('u.locked = :locked')]
                );
                $qb->expects($self->exactly(2))->method('setParameter')->withConsecutive(
                    [
                        $self->equalTo('enabled'),
                        $self->equalTo(true),
                    ],
                    [
                        $self->equalTo('locked'),
                        $self->equalTo(true),
                    ]
                );
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
            })
            ->getPager(['enabled' => true, 'locked' => true], 1);
    }

    public function testGetPagerWithDisabledAndLockedUsers()
    {
        $self = $this;
        $this
            ->getUserManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['u']));
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    [$self->equalTo('u.enabled = :enabled')],
                    [$self->equalTo('u.locked = :locked')]
                );
                $qb->expects($self->exactly(2))->method('setParameter')->withConsecutive(
                    [
                        $self->equalTo('enabled'),
                        $self->equalTo(false),
                    ],
                    [
                        $self->equalTo('locked'),
                        $self->equalTo(true),
                    ]
                );
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('u.username'),
                    $self->equalTo('ASC')
                );
            })
            ->getPager(['enabled' => false, 'locked' => true], 1);
    }

    protected function getUserManager($qbCallback)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, [
            'username',
            'email',
        ]);

        $encoder = $this->createMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $canonicalizer = $this->createMock('FOS\UserBundle\Util\CanonicalizerInterface');

        return new UserManager($encoder, $canonicalizer, $canonicalizer, $em, 'Sonata\UserBundle\Entity\BaseUser');
    }
}
