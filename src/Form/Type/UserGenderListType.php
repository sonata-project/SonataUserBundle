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

namespace Sonata\UserBundle\Form\Type;

use Sonata\CoreBundle\Form\Type\BaseStatusType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserGenderListType extends BaseStatusType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        // NEXT_MAJOR: Call the parent to set the choices
        $choices = \call_user_func([$this->class, $this->getter]);

        // Only flip choice list, if the keys are m/f/u and not labels
        if (method_exists(FormTypeInterface::class, 'setDefaultOptions') && 1 === \strlen(key($choices))) {
            $choices = array_flip($choices);
        }

        $resolver->setDefaults([
            'choices' => $choices,
            'choice_translation_domain' => 'SonataUserBundle',
        ]);

        // NEXT_MAJOR: Remove this when dropping support for SF 2.8
        if (method_exists(FormTypeInterface::class, 'setDefaultOptions')) {
            $resolver->setDefault('choices_as_values', true);
        }
    }
}
