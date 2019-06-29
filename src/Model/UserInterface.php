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

use FOS\UserBundle\Model\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface
{
    public const GENDER_FEMALE = 'f';
    public const GENDER_MALE = 'm';
    public const GENDER_UNKNOWN = 'u';

    /**
     * Sets the creation date.
     */
    public function setCreatedAt(\DateTimeInterface $createdAt = null): self;

    /**
     * Returns the creation date.
     */
    public function getCreatedAt(): ?\DateTimeInterface;

    /**
     * Sets the last update date.
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt = null): self;

    /**
     * Returns the last update date.
     */
    public function getUpdatedAt(): ?\DateTimeInterface;

    /**
     * Sets the user groups.
     */
    public function setGroups(array $groups): self;

    /**
     * Sets the two-step verification code.
     */
    public function setTwoStepVerificationCode(?string $twoStepVerificationCode): self;

    /**
     * Returns the two-step verification code.
     */
    public function getTwoStepVerificationCode(): ?string;

    public function setBiography(?string $biography): self;

    public function getBiography(): ?string;

    public function setDateOfBirth(\DateTimeInterface $dateOfBirth = null): self;

    public function getDateOfBirth(): ?\DateTimeInterface;

    public function setFacebookData(?array $facebookData): self;

    public function getFacebookData(): ?array;

    public function setFacebookName(?string $facebookName): self;

    public function getFacebookName(): ?string;

    public function setFacebookUid(?string $facebookUid): self;

    public function getFacebookUid(): ?string;

    public function setFirstname(?string $firstname): self;

    public function getFirstname(): ?string;

    public function setGender(?string $gender): self;

    public function getGender(): ?string;

    public function setGplusData(?array $gplusData): self;

    public function getGplusData(): ?array;

    public function setGplusName(?string $gplusName): self;

    public function getGplusName(): ?string;

    public function setGplusUid(?string $gplusUid): self;

    public function getGplusUid(): ?string;

    public function setLastname(?string $lastname): self;

    public function getLastname(): ?string;

    public function setLocale(?string $locale): self;

    public function getLocale(): ?string;

    public function setPhone(?string $phone): self;

    public function getPhone(): ?string;

    public function setTimezone(?string $timezone): self;

    public function getTimezone(): ?string;

    public function setTwitterData(?array $twitterData): self;

    public function getTwitterData(): ?array;

    public function setTwitterName(?string $twitterName): self;

    public function getTwitterName(): ?string;

    public function setTwitterUid(?string $twitterUid): self;

    public function getTwitterUid(): ?string;

    public function setWebsite(?string $website): self;

    public function getWebsite(): ?string;

    public function setToken(?string $token): self;

    public function getToken(): ?string;

    public function getFullname(): ?string;

    public function getRealRoles(): array;

    public function setRealRoles(array $roles): self;
}
