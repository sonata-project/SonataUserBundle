<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Document;

use FOS\UserBundle\Document\UserManager as BaseUserManager;
use Sonata\UserBundle\Model\UserManagerInterface;

/**
 * Class UserManager.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class UserManager extends BaseUserManager implements UserManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findUsersBy(array $criteria = null, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = array())
    {
        // TODO: Implement getPager() method.
        throw new \RuntimeException('method getPager() is not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        // TODO: Implement find() method.
        throw new \RuntimeException('method find() is not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        // TODO: Implement findAll() method.
        throw new \RuntimeException('method findAll() is not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        // TODO: Implement findBy() method.
        throw new \RuntimeException('method findBy() is not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        // TODO: Implement findOneBy() method.
        throw new \RuntimeException('method findOneBy() is not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        // TODO: Implement create() method.
        throw new \RuntimeException('method create() is not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function save($entity, $andFlush = true)
    {
        // TODO: Implement save() method.
        throw new \RuntimeException('method save() is not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entity, $andFlush = true)
    {
        // TODO: Implement delete() method.
        throw new \RuntimeException('method delete() is not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        // TODO: Implement getTableName() method.
        throw new \RuntimeException('method getTableName() is not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        // TODO: Implement getConnection() method.
        throw new \RuntimeException('method getConnection() is not implemented');
    }
}
