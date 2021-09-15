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

use Doctrine\Common\Collections\Collection;

interface UserInterface extends \FOS\UserBundle\Model\UserInterface
{
    public const GENDER_FEMALE = 'f';
    public const GENDER_MALE = 'm';
    public const GENDER_UNKNOWN = 'u';

    /**
     * Sets the creation date.
     *
     * @return static
     */
    public function setCreatedAt(?\DateTime $createdAt = null): UserInterface;

    /**
     * Returns the creation date.
     */
    public function getCreatedAt(): ?\DateTime;

    /**
     * Sets the last update date.
     *
     * @return static
     */
    public function setUpdatedAt(?\DateTime $updatedAt = null): UserInterface;

    /**
     * Returns the last update date.
     */
    public function getUpdatedAt(): ?\DateTime;

    /**
     * Sets the user groups.
     *
     * @param array<int, GroupInterface> $groups
     *
     * @return static
     */
    public function setGroups(array $groups): UserInterface;

    /**
     * Sets the two-step verification code.
     *
     * @return static
     */
    public function setTwoStepVerificationCode(string $twoStepVerificationCode): UserInterface;

    /**
     * Returns the two-step verification code.
     */
    public function getTwoStepVerificationCode(): ?string;

    /**
     * @return static
     */
    public function setBiography(string $biography): UserInterface;

    public function getBiography(): ?string;

    /**
     * @return static
     */
    public function setDateOfBirth(\DateTime $dateOfBirth): UserInterface;

    public function getDateOfBirth(): ?\DateTime;

    /**
     * @return static
     */
    public function setFacebookData(array $facebookData = []): UserInterface;

    public function getFacebookData(): array;

    /**
     * @return static
     */
    public function setFacebookName(?string $facebookName): UserInterface;

    public function getFacebookName(): ?string;

    /**
     * @return static
     */
    public function setFacebookUid(?string $facebookUid): UserInterface;

    public function getFacebookUid(): ?string;

    /**
     * @return static
     */
    public function setFirstname(?string $firstname): UserInterface;

    public function getFirstname(): ?string;

    /**
     * @return static
     */
    public function setGender(?string $gender): UserInterface;

    public function getGender(): ?string;

    /**
     * @return static
     */
    public function setGplusData(array $gplusData = []): UserInterface;

    public function getGplusData(): array;

    /**
     * @return static
     */
    public function setGplusName(?string $gplusName): UserInterface;

    public function getGplusName(): ?string;

    /**
     * @return static
     */
    public function setGplusUid(?string $gplusUid): UserInterface;

    public function getGplusUid(): ?string;

    /**
     * @return static
     */
    public function setLastname(?string $lastname): UserInterface;

    public function getLastname(): ?string;

    /**
     * @return static
     */
    public function setLocale(?string $locale): UserInterface;

    public function getLocale(): ?string;

    /**
     * @return static
     */
    public function setPhone(?string $phone): UserInterface;

    public function getPhone(): ?string;

    /**
     * @return static
     */
    public function setTimezone(?string $timezone): UserInterface;

    public function getTimezone(): ?string;

    /**
     * @return static
     */
    public function setTwitterData(array $twitterData = []): UserInterface;

    public function getTwitterData(): array;

    /**
     * @return static
     */
    public function setTwitterName(?string $twitterName): UserInterface;

    public function getTwitterName(): ?string;

    /**
     * @return static
     */
    public function setTwitterUid(?string $twitterUid): UserInterface;

    public function getTwitterUid(): ?string;

    /**
     * @return static
     */
    public function setWebsite(?string $website): UserInterface;

    public function getWebsite(): ?string;

    /**
     * @return static
     */
    public function setToken(?string $token): UserInterface;

    public function getToken(): ?string;

    public function getFullname(): ?string;

    /**
     * @return array<int, string>
     */
    public function getRealRoles(): array;

    /**
     * @param array<int, string> $roles
     *
     * @return static
     */
    public function setRealRoles(array $roles): UserInterface;

    public function getGroups(): Collection;

    public function getGroupNames(): array;

    public function hasGroup(string $name): bool;

    public function addGroup(GroupInterface $group): UserInterface;

    public function removeGroup(GroupInterface $group): UserInterface;

    public function isAccountNonLocked(): bool;

    public function getUserIdentifier(): ?string;
}
