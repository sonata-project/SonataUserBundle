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
use Sonata\BlockBundle\Util\OptionsResolver;
use Sonata\UserBundle\Entity\BaseUser;
use Sonata\UserBundle\Form\Type\UserGenderListType;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Jordi Sala <jordism91@gmail.com>
 */
final class UserGenderListTypeTest extends TestCase
{
    public function testChoices(): void
    {
        $type = new UserGenderListType(
            BaseUser::class,
            'getGenderList',
            UserGenderListType::class
        );

        $resolver = new OptionsResolver();

        $type->configureOptions($resolver);

        $choices = [
            'gender_unknown' => UserInterface::GENDER_UNKNOWN,
            'gender_female' => UserInterface::GENDER_FEMALE,
            'gender_male' => UserInterface::GENDER_MALE,
        ];

        // NEXT_MAJOR: Remove this when dropping support for SF 2.8
        if (method_exists(FormTypeInterface::class, 'setDefaultOptions')) {
            $choices = array_flip($choices);
        }

        $options = $resolver->resolve();

        $this->assertEquals($choices, $options['choices']);
    }
}
