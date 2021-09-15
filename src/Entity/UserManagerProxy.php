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

namespace Sonata\UserBundle\Entity;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\Doctrine\Entity\BaseEntityManager;
use Sonata\UserBundle\Model\UserInterface;

/**
 * This UserManageProxy class is used to keep UserManager compatible with Sonata ManagerInterface implementation
 * because UserManager implements FOSUserBundle manager interface.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class UserManagerProxy extends BaseEntityManager
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @param string $class
     */
    public function __construct($class, ManagerRegistry $registry, UserManager $userManager)
    {
        parent::__construct($class, $registry);

        $this->userManager = $userManager;
    }

    public function getClass(): ?string
    {
        return $this->userManager->getClass();
    }

    /**
     * @return array<int, UserInterface>
     */
    public function findAll(): array
    {
        return $this->userManager->findAll();
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array<int, UserInterface>
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->userManager->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): UserInterface
    {
        return $this->userManager->findOneBy($criteria, $orderBy);
    }

    /**
     * @param int $id
     */
    public function find($id): ?UserInterface
    {
        return $this->userManager->find($id);
    }

    public function create(): UserInterface
    {
        return $this->userManager->create();
    }

    /**
     * @param UserInterface $entity
     * @param bool $andFlush
     */
    public function save($entity, $andFlush = true): void
    {
        $this->userManager->save($entity, $andFlush);
    }

    /**
     * @param UserInterface $entity
     * @param bool $andFlush
     */
    public function delete($entity, $andFlush = true): void
    {
        $this->userManager->delete($entity, $andFlush);
    }

    public function getTableName(): string
    {
        return $this->userManager->getTableName();
    }

    public function getConnection(): Connection
    {
        return $this->userManager->getConnection();
    }
}
