<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\UserBundle\Document;

use FOS\UserBundle\Document\UserManager as BaseUserManager;
use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;

/**
 * Class UserManager
 *
 * @package Sonata\UserBundle\Document
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
    public function find($id)
    {
        // TODO: Implement find() method.
        throw new MethodNotImplementedException('find');
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        // TODO: Implement findAll() method.
        throw new MethodNotImplementedException('findAll');
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        // TODO: Implement findBy() method.
        throw new MethodNotImplementedException('findBy');
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        // TODO: Implement findOneBy() method.
        throw new MethodNotImplementedException('findOneBy');
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        // TODO: Implement create() method.
        throw new MethodNotImplementedException('create');
    }

    /**
     * {@inheritdoc}
     */
    public function save($entity, $andFlush = true)
    {
        // TODO: Implement save() method.
        throw new MethodNotImplementedException('save');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entity, $andFlush = true)
    {
        // TODO: Implement delete() method.
        throw new MethodNotImplementedException('delete');
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        // TODO: Implement getTableName() method.
        throw new MethodNotImplementedException('getTableName');
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        // TODO: Implement getConnection() method.
        throw new MethodNotImplementedException('getConnection');
    }
}