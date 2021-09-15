<?php

namespace Sonata\UserBundle\Model;

abstract class Group implements GroupInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array<int, string>
     */
    protected $roles;

    public function __construct(?string $name = null, array $roles = [])
    {
        $this->name = $name;
        $this->roles = $roles;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): GroupInterface
    {
        $this->name = $name;

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->roles, true);
    }

    /**
     * @return array<int, string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(string $role): GroupInterface
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = strtoupper($role);
        }

        return $this;
    }

    public function removeRole(string $role): GroupInterface
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function setRoles(array $roles): GroupInterface
    {
        $this->roles = $roles;

        return $this;
    }
}
