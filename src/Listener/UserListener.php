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
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Util\CanonicalFieldsUpdaterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @internal
 */
final class UserListener implements EventSubscriber
{
    /**
     * @var CanonicalFieldsUpdaterInterface
     */
    private $canonicalFieldsUpdater;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(
        CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof UserInterface) {
            return;
        }

        $this->updateUser($object);
    }

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

        $plainPassword = $user->getPlainPassword();

        if (null === $plainPassword) {
            return;
        }

        $password = $this->userPasswordEncoder->encodePassword($user, $plainPassword);

        $user->setPassword($password);
        $user->eraseCredentials();
    }

    private function recomputeChangeSet(ObjectManager $om, UserInterface $user): void
    {
        $meta = $om->getClassMetadata(\get_class($user));

        if ($om instanceof EntityManager) {
            $om->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
        } elseif ($om instanceof DocumentManager) {
            $om->getUnitOfWork()->recomputeSingleDocumentChangeSet($meta, $user);
        }
    }
}
