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

@trigger_error(
    'The '.__NAMESPACE__.'\UserGenderListType class is deprecated since version 4.1 and will be removed in 5.0.'
    .' Use Symfony\Component\Form\Extension\Core\Type\ChoiceType instead.',
    E_USER_DEPRECATED
);

/**
 * NEXT_MAJOR: remove this class.
 *
 * @deprecated since version 4.1, to be removed in 5.0.
 * Use Symfony\Component\Form\Extension\Core\Type\ChoiceType instead
 */
class UserGenderListType extends BaseStatusType
{
}
