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
use Sonata\UserBundle\Entity\BaseGroup;

class BaseGroupTest extends TestCase
{
    public function testToString(): void
    {
        // Given
        $group = new BaseGroup('Group');

        // When
        $string = (string) $group;

        // Then
        $this->assertSame('Group', $string, 'Should return the group name as string representation');
    }
}
