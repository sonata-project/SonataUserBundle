<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\UserBundle\Model;

use FOS\UserBundle\Model\UserManagerInterface as BaseInterface;

/**
 * Class UserManagerInterface
 *
 * @package Sonata\UserBundle\Model
 *
 * @author Hugo Briand <briand@ekino.com>
 */
interface UserManagerInterface extends BaseInterface
{
    /**
     * Alias for the repository method
     *
     * @param array|null $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return UserInterface[]
     */
    public function findUsersBy(array $criteria = null, array $orderBy = null, $limit = null, $offset = null);

    /**
     * {@inheritdoc}
     */
    public function find($id);

    /**
     * {@inheritdoc}
     */
    public function findAll();

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null);

    /**
     * {@inheritdoc}
     */
    public function create();

    /**
     * {@inheritdoc}
     */
    public function save($entity, $andFlush = true);

    /**
     * {@inheritdoc}
     */
    public function delete($entity, $andFlush = true);

    /**
     * {@inheritdoc}
     */
    public function getTableName();

    /**
     * {@inheritdoc}
     */
    public function getConnection();
}