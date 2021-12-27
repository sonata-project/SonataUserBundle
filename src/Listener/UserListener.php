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
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Util\CanonicalFieldsUpdaterInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @internal
 */
final class UserListener implements EventSubscriber
{
    private CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater;

    /**
     * @psalm-suppress DeprecatedClass
     *
     * @var UserPasswordEncoderInterface|UserPasswordHasherInterface
     */
    private object $userPasswordHasher;

    /**
     * TODO: Simplify this once support for Symfony 4.4 is dropped.
     *
     * @psalm-suppress DeprecatedClass
     *
     * @param UserPasswordEncoderInterface|UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(
        CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater,
        object $userPasswordHasher
    ) {
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
        $this->userPasswordHasher = $userPasswordHasher;
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

        if ($this->userPasswordHasher instanceof UserPasswordHasherInterface) {
            $password = $this->userPasswordHasher->hashPassword($user, $plainPassword);
        } else {
            $password = $this->userPasswordHasher->encodePassword($user, $plainPassword);
        }

        $user->setPassword($password);
        $user->eraseCredentials();
    }

    private function recomputeChangeSet(ObjectManager $om, UserInterface $user): void
    {
        $meta = $om->getClassMetadata(\get_class($user));

        if ($om instanceof EntityManager) {
            \assert($meta instanceof ORMClassMetadata);

            $om->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
        } elseif ($om instanceof DocumentManager) {
            \assert($meta instanceof ODMClassMetadata);

            $om->getUnitOfWork()->recomputeSingleDocumentChangeSet($meta, $user);
        }
    }
}
