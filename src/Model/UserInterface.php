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
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface, EquatableInterface, BCPasswordAuthenticatedUserInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public const GENDER_FEMALE = 'f';
    public const GENDER_MALE = 'm';
    public const GENDER_UNKNOWN = 'u';

    /**
     * @return int|string|null
     */
    public function getId();

    public function setUsername(string $username): void;

    public function getUsernameCanonical(): ?string;

    public function setUsernameCanonical(string $usernameCanonical): void;

    public function setSalt(?string $salt): void;

    public function getEmail(): ?string;

    public function setEmail(?string $email): void;

    public function getEmailCanonical(): ?string;

    public function setEmailCanonical(?string $emailCanonical): void;

    public function getPlainPassword(): ?string;

    public function setPlainPassword(string $password): void;

    public function setPassword(?string $password): void;

    /**
     * TODO: Remove this method when dropping support for Symfony 4.
     */
    public function getPassword(): ?string;

    public function isSuperAdmin(): bool;

    public function setEnabled(bool $enabled): void;

    public function setSuperAdmin(bool $boolean): void;

    public function getConfirmationToken(): ?string;

    public function setConfirmationToken(?string $confirmationToken): void;

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

    /**
     * Sets the creation date.
     *
     * @return UserInterface
     */
    public function setCreatedAt(?\DateTime $createdAt = null);

    /**
     * Returns the creation date.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt();

    /**
     * Sets the last update date.
     *
     * @return UserInterface
     */
    public function setUpdatedAt(?\DateTime $updatedAt = null);

    /**
     * Returns the last update date.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt();

    /**
     * Sets the user groups.
     *
     * @param array $groups
     *
     * @return UserInterface
     */
    public function setGroups($groups);

    /**
     * @param string $biography
     *
     * @return UserInterface
     */
    public function setBiography($biography);

    /**
     * @return string
     */
    public function getBiography();

    /**
     * @param \DateTime $dateOfBirth
     *
     * @return UserInterface
     */
    public function setDateOfBirth($dateOfBirth);

    /**
     * @return \DateTime|null
     */
    public function getDateOfBirth();

    /**
     * @param string $facebookData
     *
     * @return UserInterface
     */
    public function setFacebookData($facebookData);

    /**
     * @return string
     */
    public function getFacebookData();

    /**
     * @param string $facebookName
     *
     * @return UserInterface
     */
    public function setFacebookName($facebookName);

    /**
     * @return string
     */
    public function getFacebookName();

    /**
     * @param string $facebookUid
     *
     * @return UserInterface
     */
    public function setFacebookUid($facebookUid);

    /**
     * @return string
     */
    public function getFacebookUid();

    /**
     * @param string $firstname
     *
     * @return UserInterface
     */
    public function setFirstname($firstname);

    /**
     * @return string
     */
    public function getFirstname();

    /**
     * @param string $gender
     *
     * @return UserInterface
     */
    public function setGender($gender);

    /**
     * @return string
     */
    public function getGender();

    /**
     * @param string $gplusData
     *
     * @return UserInterface
     */
    public function setGplusData($gplusData);

    /**
     * @return string
     */
    public function getGplusData();

    /**
     * @param string $gplusName
     *
     * @return UserInterface
     */
    public function setGplusName($gplusName);

    /**
     * @return string
     */
    public function getGplusName();

    /**
     * @param string $gplusUid
     *
     * @return UserInterface
     */
    public function setGplusUid($gplusUid);

    /**
     * @return string
     */
    public function getGplusUid();

    /**
     * @param string $lastname
     *
     * @return UserInterface
     */
    public function setLastname($lastname);

    /**
     * @return string
     */
    public function getLastname();

    /**
     * @param string $locale
     *
     * @return UserInterface
     */
    public function setLocale($locale);

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $phone
     *
     * @return UserInterface
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $timezone
     *
     * @return UserInterface
     */
    public function setTimezone($timezone);

    /**
     * @return string
     */
    public function getTimezone();

    /**
     * @param string $twitterData
     *
     * @return UserInterface
     */
    public function setTwitterData($twitterData);

    /**
     * @return string
     */
    public function getTwitterData();

    /**
     * @param string $twitterName
     *
     * @return UserInterface
     */
    public function setTwitterName($twitterName);

    /**
     * @return string
     */
    public function getTwitterName();

    /**
     * @param string $twitterUid
     *
     * @return UserInterface
     */
    public function setTwitterUid($twitterUid);

    /**
     * @return string
     */
    public function getTwitterUid();

    /**
     * @param string $website
     *
     * @return UserInterface
     */
    public function setWebsite($website);

    /**
     * @return string
     */
    public function getWebsite();

    /**
     * @return string
     */
    public function getFullname();

    /**
     * @return array
     */
    public function getRealRoles();

    /**
     * @return UserInterface
     */
    public function setRealRoles(array $roles);
}
