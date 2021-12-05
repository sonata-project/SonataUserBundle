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

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use Sonata\Doctrine\Model\ManagerInterface;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManagerInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class UserManager extends BaseUserManager implements UserManagerInterface, ManagerInterface
{
    public function findUsersBy(?array $criteria = null, ?array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
        return parent::findUserBy($criteria);
    }

    public function create()
    {
        return parent::createUser();
    }

    public function save($entity, $andFlush = true): void
    {
        if (!$entity instanceof UserInterface) {
            throw new \InvalidArgumentException('Save method expected entity of type UserInterface');
        }

        parent::updateUser($entity, $andFlush);
    }

    public function delete($entity, $andFlush = true): void
    {
        if (!$entity instanceof UserInterface) {
            throw new \InvalidArgumentException('Save method expected entity of type UserInterface');
        }

        parent::deleteUser($entity);
    }

    public function getTableName()
    {
        return $this->objectManager->getClassMetadata($this->getClass())->table['name'];
    }

    public function getConnection()
    {
        return $this->objectManager->getConnection();
    }
}
