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
use Sonata\UserBundle\Util\CanonicalFieldsUpdaterInterface;
use Symfony\Component\Validator\ObjectInitializerInterface;

/**
 * @internal
 */
final class UserInitializer implements ObjectInitializerInterface
{
    private CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater;

    public function __construct(CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater)
    {
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    /**
     * @param object $object
     */
    public function initialize($object): void
    {
        if (!$object instanceof UserInterface) {
            return;
        }

        $this->canonicalFieldsUpdater->updateCanonicalFields($object);
    }
}
