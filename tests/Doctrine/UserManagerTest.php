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

namespace Sonata\UserBundle\Tests\Doctrine;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Doctrine\UserManager;
use Sonata\UserBundle\Model\User;

class UserManagerTest extends TestCase
{
    public const USER_CLASS = 'Sonata\UserBundle\Tests\Doctrine\DummyUser';

    /** @var UserManager */
    protected $userManager;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $om;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    protected function setUp(): void
    {
        if (!interface_exists('Doctrine\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }

        $passwordUpdater = $this->getMockBuilder('Sonata\UserBundle\Util\PasswordUpdaterInterface')->getMock();
        $fieldsUpdater = $this->getMockBuilder('Sonata\UserBundle\Util\CanonicalFieldsUpdater')
            ->disableOriginalConstructor()
            ->getMock();
        $class = $this->getMockBuilder('Doctrine\Persistence\Mapping\ClassMetadata')->getMock();
        $this->om = $this->getMockBuilder('Doctrine\Persistence\ObjectManager')->getMock();
        $this->repository = $this->getMockBuilder('Doctrine\Persistence\ObjectRepository')->getMock();

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::USER_CLASS))
            ->willReturn($this->repository);
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::USER_CLASS))
            ->willReturn($class);
        $class->expects($this->any())
            ->method('getName')
            ->willReturn(static::USER_CLASS);

        $this->userManager = new UserManager($passwordUpdater, $fieldsUpdater, $this->om, static::USER_CLASS);
    }

    public function testDeleteUser()
    {
        $user = $this->getUser();
        $this->om->expects($this->once())->method('remove')->with($this->equalTo($user));
        $this->om->expects($this->once())->method('flush');

        $this->userManager->deleteUser($user);
    }

    public function testGetClass()
    {
        $this->assertSame(static::USER_CLASS, $this->userManager->getClass());
    }

    public function testFindUserBy()
    {
        $crit = ['foo' => 'bar'];
        $this->repository->expects($this->once())->method('findOneBy')->with($this->equalTo($crit))->willReturn([]);

        $this->userManager->findUserBy($crit);
    }

    public function testFindUsers()
    {
        $this->repository->expects($this->once())->method('findAll')->willReturn([]);

        $this->userManager->findUsers();
    }

    public function testUpdateUser()
    {
        $user = $this->getUser();
        $this->om->expects($this->once())->method('persist')->with($this->equalTo($user));
        $this->om->expects($this->once())->method('flush');

        $this->userManager->updateUser($user);
    }

    /**
     * @return mixed
     */
    protected function getUser()
    {
        $userClass = static::USER_CLASS;

        return new $userClass();
    }
}

class DummyUser extends User
{
}
