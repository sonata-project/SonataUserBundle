<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Validator;

use Sonata\UserBundle\Model\FOSUserInterface;
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
        if ($object instanceof FOSUserInterface) {
            $this->canonicalFieldsUpdater->updateCanonicalFields($object);
        }
    }
}
