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

namespace Sonata\UserBundle\Doctrine;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\UserBundle\Model\GroupInterface;
use Sonata\UserBundle\Model\GroupManager as BaseGroupManager;

class GroupManager extends BaseGroupManager
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * GroupManager constructor.
     *
     * @param string $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);

        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup(GroupInterface $group)
    {
        $this->objectManager->remove($group);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function findGroupBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findGroups()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function updateGroup(GroupInterface $group, $andFlush = true)
    {
        $this->objectManager->persist($group);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findGroupsBy(?array $criteria = null, ?array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, int $page, int $limit = 10, array $sort = []): PagerInterface
    {
        $query = $this->repository
            ->createQueryBuilder('g')
            ->select('g');

        $fields = $this->objectManager->getClassMetadata($this->class)->getFieldNames();
        foreach ($sort as $field => $direction) {
            if (!\in_array($field, $fields, true)) {
                throw new \RuntimeException(sprintf("Invalid sort field '%s' in '%s' class", $field, $this->class));
            }
        }

        if (0 === \count($sort)) {
            $sort = ['name' => 'ASC'];
        }

        foreach ($sort as $field => $direction) {
            $query->orderBy(sprintf('g.%s', $field), strtoupper($direction));
        }

        $parameters = [];

        if (isset($criteria['enabled'])) {
            $query->andWhere('g.enabled = :enabled');
            $parameters['enabled'] = $criteria['enabled'];
        }

        $query->setParameters($parameters);

        return Pager::create($query, $limit, $page);
    }
}
