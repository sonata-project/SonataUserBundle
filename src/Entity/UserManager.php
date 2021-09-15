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
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\Doctrine\Model\ManagerInterface;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManager as BaseUserManager;
use Sonata\UserBundle\Model\UserManagerInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class UserManager extends BaseUserManager implements UserManagerInterface, ManagerInterface
{
    /**
     * @param int $id
     */
    public function find($id): ?UserInterface
    {
        return $this->getRepository()->find($id);
    }

    /**
     * @return array<int, UserInterface>
     */
    public function findAll(): array
    {
        return $this->getRepository()->findAll();
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array<int, UserInterface>
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): UserInterface
    {
        return parent::findUserBy($criteria);
    }

    public function create(): UserInterface
    {
        return parent::createUser();
    }

    /**
     * @param bool $andFlush
     */
    public function save($entity, $andFlush = true): void
    {
        if (!$entity instanceof UserInterface) {
            throw new \InvalidArgumentException('Save method expected entity of type UserInterface');
        }

        parent::updateUser($entity, $andFlush);
    }

    /**
     * @param bool $andFlush
     */
    public function delete($entity, $andFlush = true): void
    {
        if (!$entity instanceof UserInterface) {
            throw new \InvalidArgumentException('Save method expected entity of type UserInterface');
        }

        parent::deleteUser($entity);
    }

    public function getTableName(): string
    {
        return $this->objectManager->getClassMetadata($this->getClass())->table['name'];
    }

    public function getConnection(): Connection
    {
        return $this->objectManager->getConnection();
    }

    public function getPager(array $criteria, int $page, int $limit = 10, array $sort = []): PagerInterface
    {
        $query = $this->getRepository()
            ->createQueryBuilder('u')
            ->select('u');

        $fields = $this->objectManager->getClassMetadata($this->getClass())->getFieldNames();
        foreach ($sort as $field => $direction) {
            if (!\in_array($field, $fields, true)) {
                throw new \RuntimeException(
                    sprintf("Invalid sort field '%s' in '%s' class", $field, $this->getClass())
                );
            }
        }
        if (0 === \count($sort)) {
            $sort = ['username' => 'ASC'];
        }
        foreach ($sort as $field => $direction) {
            $query->orderBy(sprintf('u.%s', $field), strtoupper($direction));
        }

        if (isset($criteria['enabled'])) {
            $query->andWhere('u.enabled = :enabled');
            $query->setParameter('enabled', $criteria['enabled']);
        }

        return Pager::create($query, $limit, $page);
    }
}
