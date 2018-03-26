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

namespace Sonata\UserBundle\Tests\Form\Type;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Entity\BaseUser;
use Sonata\UserBundle\Form\Type\UserGenderListType;

/**
 * @author Jordi Sala <jordism91@gmail.com>
 */
final class UserGenderListTypeTest extends TestCase
{
    /**
     * @group legacy
     */
    public function testDeprecatedUserGenderListType(): void
    {
        $userGenderListType = new UserGenderListType(
            BaseUser::class,
            'getGenderLists',
            UserGenderListType::class
        );

        $this->assertNotNull($userGenderListType);
    }
}
