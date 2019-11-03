<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Doctrine\CouchDB;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\CouchDB\Event;
use Doctrine\ODM\CouchDB\Event\LifecycleEventArgs;
use Sonata\UserBundle\Model\FOSUserInterface;
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
        return array(
            Event::prePersist,
            Event::preUpdate,
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getDocument();
        if ($object instanceof FOSUserInterface) {
            $this->updateUserFields($object);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getDocument();
        if ($object instanceof FOSUserInterface) {
            $this->updateUserFields($object);
        }
    }

    /**
     * Updates the user properties.
     *
     * @param FOSUserInterface $user
     */
    private function updateUserFields(FOSUserInterface $user)
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
        $this->passwordUpdater->hashPassword($user);
    }
}
