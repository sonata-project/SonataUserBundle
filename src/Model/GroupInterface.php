<?php

namespace Sonata\UserBundle\Model;

interface GroupInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    /**
     * @return static
     */
    public function setName(string $name): GroupInterface;

    public function hasRole(string $role): bool;

    /**
     * @return array<int, string>
     */
    public function getRoles(): array;

    /**
     * @return static
     */
    public function addRole(string $role): GroupInterface;

    /**
     * @return static
     */
    public function removeRole(string $role): GroupInterface;

    /**
     * @return static
     */
    public function setRoles(array $roles): GroupInterface;
}
