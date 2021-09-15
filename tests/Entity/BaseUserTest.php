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
        static::assertInstanceOf(\DateTime::class, $user->getCreatedAt(), 'Should return a DateTime object');
        static::assertSame($today->format('U'), $user->getCreatedAt()->format('U'), 'Should contain today\'s date');

        static::assertInstanceOf(\DateTime::class, $user->getUpdatedAt(), 'Should return a DateTime object');
        static::assertSame($today->format('U'), $user->getUpdatedAt()->format('U'), 'Should contain today\'s date');
    }

    public function testDateWithPrePersist(): void
    {
        // Given
        $user = new BaseUser();
        $today = new \DateTime();

        // When
        $user->prePersist();

        // Then
        static::assertInstanceOf(\DateTime::class, $user->getCreatedAt(), 'Should contain a DateTime object');
        static::assertSame($today->format('Y-m-d'), $user->getUpdatedAt()->format('Y-m-d'), 'Should be created today');

        static::assertInstanceOf(\DateTime::class, $user->getUpdatedAt(), 'Should contain a DateTime object');
        static::assertSame($today->format('Y-m-d'), $user->getUpdatedAt()->format('Y-m-d'), 'Should be updated today');
    }

    public function testDateWithPreUpdate(): void
    {
        // Given
        $user = new BaseUser();
        $user->setCreatedAt(\DateTime::createFromFormat('Y-m-d', '2012-01-01'));
        $today = new \DateTime();

        // When
        $user->preUpdate();

        // Then
        static::assertInstanceOf(\DateTime::class, $user->getCreatedAt(), 'Should contain a DateTime object');
        static::assertSame('2012-01-01', $user->getCreatedAt()->format('Y-m-d'), 'Should be created at 2012-01-01.');

        static::assertInstanceOf(\DateTime::class, $user->getUpdatedAt(), 'Should contain a DateTime object');
        static::assertSame($today->format('Y-m-d'), $user->getUpdatedAt()->format('Y-m-d'), 'Should be updated today');
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

    public function testTwoStepVerificationCode(): void
    {
        // Given
        $user = new BaseUser();

        // When
        $user->setTwoStepVerificationCode('123456');

        // Then
        static::assertSame('123456', $user->getTwoStepVerificationCode(), 'Should return the two step verification code');
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
