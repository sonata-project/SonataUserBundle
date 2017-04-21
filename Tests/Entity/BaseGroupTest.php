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

use Sonata\UserBundle\Entity\BaseGroup;

class BaseGroupTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        // Given
        $group = new BaseGroup('Group');

        // When
        $string = (string) $group;

        // Then
        $this->assertEquals('Group', $string, 'Should return the group name as string representation');
    }
}
