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

namespace Sonata\UserBundle\Model;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface, EquatableInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @return int|string|null
     */
    public function getId();

    public function setUsername(string $username): void;

    public function getUsernameCanonical(): ?string;

    public function setUsernameCanonical(?string $usernameCanonical): void;

    public function setSalt(?string $salt): void;

    public function getEmail(): ?string;

    public function setEmail(?string $email): void;

    public function getEmailCanonical(): ?string;

    public function setEmailCanonical(?string $emailCanonical): void;

    public function getPlainPassword(): ?string;

    public function setPlainPassword(?string $password): void;

    public function setPassword(?string $password): void;

    /**
     * TODO: Remove this method when dropping support for Symfony 5.
     */
    public function getPassword(): ?string;

    public function isSuperAdmin(): bool;

    public function setEnabled(bool $enabled): void;

    public function setSuperAdmin(bool $boolean): void;

    public function getConfirmationToken(): ?string;

    public function setConfirmationToken(?string $confirmationToken): void;

    public function getPasswordRequestedAt(): ?\DateTimeInterface;

    public function setPasswordRequestedAt(?\DateTimeInterface $date = null): void;

    public function isPasswordRequestNonExpired(int $ttl): bool;

    public function setLastLogin(?\DateTimeInterface $time = null): void;

    public function hasRole(string $role): bool;

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void;

    public function addRole(string $role): void;

    public function removeRole(string $role): void;

    public function isAccountNonExpired(): bool;

    public function isAccountNonLocked(): bool;

    public function isCredentialsNonExpired(): bool;

    public function isEnabled(): bool;

    public function setCreatedAt(?\DateTimeInterface $createdAt = null): void;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(?\DateTimeInterface $updatedAt = null): void;

    public function getUpdatedAt(): ?\DateTimeInterface;

    /**
     * @return string[]
     */
    public function getRealRoles(): array;

    /**
     * @param string[] $roles
     */
    public function setRealRoles(array $roles): void;
}
