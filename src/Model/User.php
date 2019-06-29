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

use FOS\UserBundle\Model\User as AbstractedUser;

/**
 * Represents a User model.
 */
abstract class User extends AbstractedUser implements UserInterface
{
    /**
     * @var \DateTimeImmutable|null
     */
    protected $createdAt;

    /**
     * @var \DateTimeImmutable|null
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $twoStepVerificationCode;

    /**
     * @var \DateTimeImmutable|null
     */
    protected $dateOfBirth;

    /**
     * @var string
     */
    protected $firstname;

    /**
     * @var string
     */
    protected $lastname;

    /**
     * @var string
     */
    protected $website;

    /**
     * @var string
     */
    protected $biography;

    /**
     * @var string
     */
    protected $gender = self::GENDER_UNKNOWN; // set the default to unknown

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $facebookUid;

    /**
     * @var string
     */
    protected $facebookName;

    /**
     * @var string
     */
    protected $facebookData;

    /**
     * @var string
     */
    protected $twitterUid;

    /**
     * @var string
     */
    protected $twitterName;

    /**
     * @var string
     */
    protected $twitterData;

    /**
     * @var string
     */
    protected $gplusUid;

    /**
     * @var string
     */
    protected $gplusName;

    /**
     * @var string
     */
    protected $gplusData;

    /**
     * @var string
     */
    protected $token;

    /**
     * Returns a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername() ?: '-';
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTimeInterface $createdAt = null): UserInterface
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt = null): UserInterface
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setGroups(array $groups): UserInterface
    {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTwoStepVerificationCode(?string $twoStepVerificationCode): UserInterface
    {
        $this->twoStepVerificationCode = $twoStepVerificationCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTwoStepVerificationCode(): ?string
    {
        return $this->twoStepVerificationCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setBiography(?string $biography): UserInterface
    {
        $this->biography = $biography;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBiography(): ?string
    {
        return $this->biography;
    }

    /**
     * {@inheritdoc}
     */
    public function setDateOfBirth(\DateTimeInterface $dateOfBirth = null): UserInterface
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    /**
     * {@inheritdoc}
     */
    public function setFacebookData(?array $facebookData): UserInterface
    {
        $this->facebookData = $facebookData;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacebookData(): ?array
    {
        return $this->facebookData;
    }

    /**
     * {@inheritdoc}
     */
    public function setFacebookName(?string $facebookName): UserInterface
    {
        $this->facebookName = $facebookName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacebookName(): ?string
    {
        return $this->facebookName;
    }

    /**
     * {@inheritdoc}
     */
    public function setFacebookUid(?string $facebookUid): UserInterface
    {
        $this->facebookUid = $facebookUid;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacebookUid(): ?string
    {
        return $this->facebookUid;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstname(?string $firstname): UserInterface
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * {@inheritdoc}
     */
    public function setGender(?string $gender): UserInterface
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * {@inheritdoc}
     */
    public function setGplusData(?array $gplusData): UserInterface
    {
        $this->gplusData = $gplusData;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGplusData(): ?array
    {
        return $this->gplusData;
    }

    /**
     * {@inheritdoc}
     */
    public function setGplusName(?string $gplusName): UserInterface
    {
        $this->gplusName = $gplusName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGplusName(): ?string
    {
        return $this->gplusName;
    }

    /**
     * {@inheritdoc}
     */
    public function setGplusUid(?string $gplusUid): UserInterface
    {
        $this->gplusUid = $gplusUid;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGplusUid(): ?string
    {
        return $this->gplusUid;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastname(?string $lastname): UserInterface
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale(?string $locale): UserInterface
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setPhone(?string $phone): UserInterface
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * {@inheritdoc}
     */
    public function setTimezone(?string $timezone): UserInterface
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function setTwitterData(?array $twitterData): UserInterface
    {
        $this->twitterData = $twitterData;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTwitterData(): ?array
    {
        return $this->twitterData;
    }

    /**
     * {@inheritdoc}
     */
    public function setTwitterName(?string $twitterName): UserInterface
    {
        $this->twitterName = $twitterName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTwitterName(): ?string
    {
        return $this->twitterName;
    }

    /**
     * {@inheritdoc}
     */
    public function setTwitterUid(?string $twitterUid): UserInterface
    {
        $this->twitterUid = $twitterUid;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTwitterUid(): ?string
    {
        return $this->twitterUid;
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsite(?string $website): UserInterface
    {
        $this->website = $website;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(?string $token): UserInterface
    {
        $this->token = $token;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullname(): ?string
    {
        return sprintf('%s %s', $this->getFirstname(), $this->getLastname());
    }

    /**
     * {@inheritdoc}
     */
    public function getRealRoles(): array
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function setRealRoles(array $roles): UserInterface
    {
        $this->setRoles($roles);

        return $this;
    }

    /**
     * Returns the gender list.
     */
    public static function getGenderList(): array
    {
        return [
            'gender_unknown' => self::GENDER_UNKNOWN,
            'gender_female' => self::GENDER_FEMALE,
            'gender_male' => self::GENDER_MALE,
        ];
    }
}
