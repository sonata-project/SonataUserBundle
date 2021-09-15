<?php

namespace Sonata\UserBundle\Model;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

abstract class GroupManager implements GroupManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $class;

    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->class = $class;
    }

    public function createGroup(string $name): GroupInterface
    {
        $class = $this->getClass();

        return new $class($name);
    }

    public function deleteGroup(GroupInterface $group): void
    {
        $this->objectManager->remove($group);
        $this->objectManager->flush();
    }

    public function findGroupBy(array $criteria): ?GroupInterface
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function findGroupByName(string $name): ?GroupInterface
    {
        return $this->findGroupBy(['name' => $name]);
    }

    /**
     * @return array<int, GroupInterface>
     */
    public function findGroups(): array
    {
        return $this->getRepository()->findAll();
    }

    public function getClass(): string
    {
        if (false !== strpos($this->class, ':')) {
            $metadata = $this->objectManager->getClassMetadata($this->class);
            $this->class = $metadata->getName();
        }

        return $this->class;
    }

    public function updateGroup(GroupInterface $group, bool $andFlush = true): void
    {
        $this->objectManager->persist($group);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->objectManager->getRepository($this->getClass());
    }

    /**
     * @return array<int, GroupInterface>
     */
    public function findGroupsBy(
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }
}
