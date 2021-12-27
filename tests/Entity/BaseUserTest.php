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

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Entity\BaseUser;
use Sonata\UserBundle\Model\GroupInterface;

class BaseUserTest extends TestCase
{
    public function testDateSetters(): void
    {
        // Given
        $user = new BaseUser();
        $today = new \DateTime();

        // When
        $user->setCreatedAt($today);
        $user->setUpdatedAt($today);

        // Then
        $createdAt = $user->getCreatedAt();
        $updatedAt = $user->getUpdatedAt();

        static::assertInstanceOf(\DateTime::class, $createdAt, 'Should return a DateTime object');
        static::assertSame($today->format('U'), $createdAt->format('U'), 'Should contain today\'s date');

        static::assertInstanceOf(\DateTime::class, $updatedAt, 'Should return a DateTime object');
        static::assertSame($today->format('U'), $updatedAt->format('U'), 'Should contain today\'s date');
    }

    public function testDateWithPrePersist(): void
    {
        // Given
        $user = new BaseUser();
        $today = new \DateTime();

        // When
        $user->prePersist();

        // Then
        $createdAt = $user->getCreatedAt();
        $updatedAt = $user->getUpdatedAt();

        static::assertInstanceOf(\DateTime::class, $createdAt, 'Should contain a DateTime object');
        static::assertSame($today->format('Y-m-d'), $createdAt->format('Y-m-d'), 'Should be created today');

        static::assertInstanceOf(\DateTime::class, $updatedAt, 'Should contain a DateTime object');
        static::assertSame($today->format('Y-m-d'), $updatedAt->format('Y-m-d'), 'Should be updated today');
    }

    public function testDateWithPreUpdate(): void
    {
        // Given
        $date = \DateTime::createFromFormat('Y-m-d', '2012-01-01');
        \assert(false !== $date);

        $user = new BaseUser();
        $user->setCreatedAt($date);
        $today = new \DateTime();

        // When
        $user->preUpdate();

        // Then
        $createdAt = $user->getCreatedAt();
        $updatedAt = $user->getUpdatedAt();

        static::assertInstanceOf(\DateTime::class, $createdAt, 'Should contain a DateTime object');
        static::assertSame('2012-01-01', $createdAt->format('Y-m-d'), 'Should be created at 2012-01-01.');

        static::assertInstanceOf(\DateTime::class, $updatedAt, 'Should contain a DateTime object');
        static::assertSame($today->format('Y-m-d'), $updatedAt->format('Y-m-d'), 'Should be updated today');
    }

    public function testSettingMultipleGroups(): void
    {
        // Given
        $user = new BaseUser();
        $group1 = $this->createMock(GroupInterface::class);
        $group1->method('getName')->willReturn('Group 1');
        $group2 = $this->createMock(GroupInterface::class);
        $group2->method('getName')->willReturn('Group 2');

        // When
        $user->setGroups([$group1, $group2]);

        // Then
        static::assertCount(2, $user->getGroups(), 'Should have 2 groups');
        static::assertTrue($user->hasGroup('Group 1'), 'Should have a group named "Group 1"');
        static::assertTrue($user->hasGroup('Group 2'), 'Should have a group named "Group 2"');
    }

    public function testToStringWithName(): void
    {
        // Given
        $user = new BaseUser();
        $user->setUsername('John');

        // When
        $string = (string) $user;

        // Then
        static::assertSame('John', $string, 'Should return the username as string representation');
    }

    public function testToStringWithoutName(): void
    {
        // Given
        $user = new BaseUser();

        // When
        $string = (string) $user;

        // Then
        static::assertSame('-', $string, 'Should return a string representation');
    }
}
