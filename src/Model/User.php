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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as AbstractedUser;

/**
 * Represents a User model.
 */
abstract class User extends AbstractedUser implements UserInterface
{
    /**
     * @var \DateTime|null
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $twoStepVerificationCode;

    /**
     * @var \DateTime|null
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
    protected $gender = UserInterface::GENDER_UNKNOWN; // set the default to unknown

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
     * @var array
     */
    protected $facebookData = [];

    /**
     * @var string
     */
    protected $twitterUid;

    /**
     * @var string
     */
    protected $twitterName;

    /**
     * @var array
     */
    protected $twitterData = [];

    /**
     * @var string
     */
    protected $gplusUid;

    /**
     * @var string
     */
    protected $gplusName;

    /**
     * @var array
     */
    protected $gplusData = [];

    /**
     * @var string
     */
    protected $token;

    /**
     * @var array<int, GroupInterface>|Collection
     */
    private $groups;

    public function __construct()
    {
        parent::__construct();

        $this->groups = new ArrayCollection();
    }

    /**
     * Returns a string representation.
     */
    public function __toString(): string
    {
        return $this->getUserIdentifier() ?: '-';
    }

    /**
     * @return static
     */
    public function setCreatedAt(?\DateTime $createdAt = null): UserInterface
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return static
     */
    public function setUpdatedAt(?\DateTime $updatedAt = null): UserInterface
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param array<int, GroupInterface> $groups
     *
     * @return static
     */
    public function setGroups(array $groups): UserInterface
    {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }

        return $this;
    }

    /**
     * @return static
     */
    public function setTwoStepVerificationCode(?string $twoStepVerificationCode): UserInterface
    {
        $this->twoStepVerificationCode = $twoStepVerificationCode;

        return $this;
    }

    public function getTwoStepVerificationCode(): ?string
    {
        return $this->twoStepVerificationCode;
    }

    /**
     * @return static
     */
    public function setBiography(?string $biography): UserInterface
    {
        $this->biography = $biography;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    /**
     * @return static
     */
    public function setDateOfBirth(?\DateTime $dateOfBirth): UserInterface
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTime
    {
        return $this->dateOfBirth;
    }

    /**
     * @return static
     */
    public function setFacebookData(array $facebookData = []): UserInterface
    {
        $this->facebookData = $facebookData;

        return $this;
    }

    public function getFacebookData(): array
    {
        return $this->facebookData;
    }

    /**
     * @return static
     */
    public function setFacebookName(?string $facebookName): UserInterface
    {
        $this->facebookName = $facebookName;

        return $this;
    }

    public function getFacebookName(): ?string
    {
        return $this->facebookName;
    }

    /**
     * @return static
     */
    public function setFacebookUid(?string $facebookUid): UserInterface
    {
        $this->facebookUid = $facebookUid;

        return $this;
    }

    public function getFacebookUid(): ?string
    {
        return $this->facebookUid;
    }

    /**
     * @return static
     */
    public function setFirstname(?string $firstname): UserInterface
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @return static
     */
    public function setGender(?string $gender): UserInterface
    {
        $this->gender = $gender;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @return static
     */
    public function setGplusData(array $gplusData = []): UserInterface
    {
        $this->gplusData = $gplusData;

        return $this;
    }

    public function getGplusData(): array
    {
        return $this->gplusData;
    }

    /**
     * @return static
     */
    public function setGplusName(?string $gplusName): UserInterface
    {
        $this->gplusName = $gplusName;

        return $this;
    }

    public function getGplusName(): ?string
    {
        return $this->gplusName;
    }

    /**
     * @return static
     */
    public function setGplusUid(?string $gplusUid): UserInterface
    {
        $this->gplusUid = $gplusUid;

        return $this;
    }

    public function getGplusUid(): ?string
    {
        return $this->gplusUid;
    }

    /**
     * @return static
     */
    public function setLastname(?string $lastname): UserInterface
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @return static
     */
    public function setLocale(?string $locale): UserInterface
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @return static
     */
    public function setPhone(?string $phone): UserInterface
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return static
     */
    public function setTimezone(?string $timezone): UserInterface
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * @return static
     */
    public function setTwitterData(array $twitterData = []): UserInterface
    {
        $this->twitterData = $twitterData;

        return $this;
    }

    public function getTwitterData(): array
    {
        return $this->twitterData;
    }

    /**
     * @return static
     */
    public function setTwitterName(?string $twitterName): UserInterface
    {
        $this->twitterName = $twitterName;

        return $this;
    }

    public function getTwitterName(): ?string
    {
        return $this->twitterName;
    }

    /**
     * @return static
     */
    public function setTwitterUid(?string $twitterUid): UserInterface
    {
        $this->twitterUid = $twitterUid;

        return $this;
    }

    public function getTwitterUid(): ?string
    {
        return $this->twitterUid;
    }

    /**
     * @return static
     */
    public function setWebsite(?string $website): UserInterface
    {
        $this->website = $website;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @return static
     */
    public function setToken(?string $token): UserInterface
    {
        $this->token = $token;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getFullname(): ?string
    {
        return sprintf('%s %s', $this->getFirstname(), $this->getLastname());
    }

    /**
     * @return array<int, string>
     */
    public function getRealRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array<int, string> $roles
     *
     * @return static
     */
    public function setRealRoles(array $roles): UserInterface
    {
        $this->setRoles($roles);

        return $this;
    }

    /**
     * Returns the gender list.
     *
     * @return array<string, string>
     */
    public static function getGenderList(): array
    {
        return [
            'gender_unknown' => UserInterface::GENDER_UNKNOWN,
            'gender_female' => UserInterface::GENDER_FEMALE,
            'gender_male' => UserInterface::GENDER_MALE,
        ];
    }

    /**
     * @return array<int, GroupInterface>|Collection
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * @return array<int, string>
     */
    public function getGroupNames(): array
    {
        $names = [];
        foreach ($this->groups as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    public function hasGroup($name): bool
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * @return static
     */
    public function addGroup(GroupInterface $group): UserInterface
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
        }

        return $this;
    }

    /**
     * @return static
     */
    public function removeGroup(GroupInterface $group): UserInterface
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }

        return $this;
    }

    public function isAccountNonLocked(): bool
    {
        return true;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_values(array_unique($roles));
    }
}
