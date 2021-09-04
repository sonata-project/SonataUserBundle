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

use Doctrine\Persistence\ManagerRegistry;
use Sonata\Doctrine\Entity\BaseEntityManager;

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

    public function getClass()
    {
        return $this->userManager->getClass();
    }

    public function findAll()
    {
        return $this->userManager->findAll();
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->userManager->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
        return $this->userManager->findOneBy($criteria, $orderBy);
    }

    public function find($id)
    {
        return $this->userManager->find($id);
    }

    public function create()
    {
        return $this->userManager->create();
    }

    public function save($entity, $andFlush = true)
    {
        $this->userManager->save($entity, $andFlush);
    }

    public function delete($entity, $andFlush = true)
    {
        $this->userManager->delete($entity, $andFlush);
    }

    public function getTableName()
    {
        return $this->userManager->getTableName();
    }

    public function getConnection()
    {
        return $this->userManager->getConnection();
    }
}
