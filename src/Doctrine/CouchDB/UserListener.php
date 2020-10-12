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

namespace Sonata\UserBundle\Doctrine\CouchDB;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\CouchDB\Event;
use Doctrine\ODM\CouchDB\Event\LifecycleEventArgs;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Util\CanonicalFieldsUpdater;
use Sonata\UserBundle\Util\PasswordUpdaterInterface;

class UserListener implements EventSubscriber
{
    private $passwordUpdater;
    private $canonicalFieldsUpdater;

    public function __construct(PasswordUpdaterInterface $passwordUpdater, CanonicalFieldsUpdater $canonicalFieldsUpdater)
    {
        $this->passwordUpdater = $passwordUpdater;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Event::prePersist,
            Event::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getDocument();
        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getDocument();
        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
        }
    }

    /**
     * Updates the user properties.
     */
    private function updateUserFields(UserInterface $user)
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
        $this->passwordUpdater->hashPassword($user);
    }
}
