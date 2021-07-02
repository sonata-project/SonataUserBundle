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

namespace Sonata\UserBundle\Validator;

use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Util\CanonicalFieldsUpdater;
use Symfony\Component\Validator\ObjectInitializerInterface;

/**
 * Automatically updates the canonical fields before validation.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class Initializer implements ObjectInitializerInterface
{
    private $canonicalFieldsUpdater;

    public function __construct(CanonicalFieldsUpdater $canonicalFieldsUpdater)
    {
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    /**
     * @param object $object
     */
    public function initialize($object)
    {
        if ($object instanceof UserInterface) {
            $this->canonicalFieldsUpdater->updateCanonicalFields($object);
        }
    }
}
