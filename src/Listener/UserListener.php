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

namespace Sonata\UserBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata as ODMClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use Sonata\UserBundle\Util\CanonicalFieldsUpdaterInterface;

/**
 * @internal
 *
 * NEXT_MAJOR: do not implement EventSubscriber interface anymore
 */
final class UserListener implements EventSubscriber
{
    public function __construct(
        private CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater,
        private UserManagerInterface $userManager
    ) {
    }

    /**
     * NEXT_MAJOR: remove this method.
     */
    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface|DocumentManager> $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof UserInterface) {
            return;
        }

        $this->updateUser($object);
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface|DocumentManager> $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof UserInterface) {
            return;
        }

        $this->updateUser($object);
        $this->recomputeChangeSet($args->getObjectManager(), $object);
    }

    private function updateUser(UserInterface $user): void
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);
    }

    private function recomputeChangeSet(ObjectManager $om, UserInterface $user): void
    {
        $meta = $om->getClassMetadata($user::class);

        if ($om instanceof EntityManager) {
            /** @phpstan-var ORMClassMetadata<UserInterface> $meta */
            \assert($meta instanceof ORMClassMetadata);

            $om->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
        } elseif ($om instanceof DocumentManager) {
            /** @phpstan-var ODMClassMetadata<UserInterface> $meta */
            \assert($meta instanceof ODMClassMetadata);

            $om->getUnitOfWork()->recomputeSingleDocumentChangeSet($meta, $user);
        }
    }
}
