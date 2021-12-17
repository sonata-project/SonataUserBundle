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
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

/**
 * Represents a User model.
 */
abstract class User implements UserInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $username;

    /**
     * @var string|null
     */
    protected $usernameCanonical;

    /**
     * @var string|null
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $emailCanonical;

    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @var string|null
     */
    protected $salt;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $plainPassword;

    /**
     * @var \DateTimeInterface|null
     */
    protected $lastLogin;

    /**
     * @var string|null
     */
    protected $confirmationToken;

    /**
     * @var \DateTimeInterface|null
     */
    protected $passwordRequestedAt;

    /**
     * @var Collection<int, GroupInterface>
     */
    protected $groups;

    /**
     * @var string[]
     */
    protected $roles = [];

    /**
     * @var \DateTime|null
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     */
    protected $updatedAt;

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

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    /**
     * Returns a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername();
    }

    public function __serialize(): array
    {
        return [
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical,
        ];
    }

    public function __unserialize(array $data): void
    {
        [
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical
        ] = $data;
    }

    public function addRole(string $role): void
    {
        $role = strtoupper($role);

        if ($role === static::ROLE_DEFAULT) {
            return;
        }

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getUserIdentifier(): string
    {
        return $this->username ?? '-';
    }

    public function getUsernameCanonical(): ?string
    {
        return $this->usernameCanonical;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
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

    public function hasRole(string $role): bool
    {
        return \in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function isAccountNonExpired(): bool
    {
        return true;
    }

    public function isAccountNonLocked(): bool
    {
        return true;
    }

    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    public function removeRole(string $role): void
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function setUsernameCanonical(?string $usernameCanonical): void
    {
        $this->usernameCanonical = $usernameCanonical;
    }

    public function setSalt(?string $salt): void
    {
        $this->salt = $salt;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function setEmailCanonical(?string $emailCanonical): void
    {
        $this->emailCanonical = $emailCanonical;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function setSuperAdmin(bool $boolean): void
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }
    }

    public function setPlainPassword(?string $password): void
    {
        $this->plainPassword = $password;
    }

    public function setLastLogin(?\DateTimeInterface $time = null): void
    {
        $this->lastLogin = $time;
    }

    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $date = null): void
    {
        $this->passwordRequestedAt = $date;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function isPasswordRequestNonExpired(int $ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
               $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function setRoles(array $roles): void
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function getGroupNames(): array
    {
        $names = [];
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    public function hasGroup(string $name): bool
    {
        return \in_array($name, $this->getGroupNames(), true);
    }

    public function addGroup(GroupInterface $group): void
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }
    }

    public function removeGroup(GroupInterface $group): void
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }
    }

    public function isEqualTo(SymfonyUserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function setCreatedAt(?\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setGroups($groups)
    {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }

        return $this;
    }

    public function setBiography($biography)
    {
        $this->biography = $biography;

        return $this;
    }

    public function getBiography()
    {
        return $this->biography;
    }

    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    public function setFacebookData($facebookData)
    {
        $this->facebookData = $facebookData;

        return $this;
    }

    public function getFacebookData()
    {
        return $this->facebookData;
    }

    public function setFacebookName($facebookName)
    {
        $this->facebookName = $facebookName;

        return $this;
    }

    public function getFacebookName()
    {
        return $this->facebookName;
    }

    public function setFacebookUid($facebookUid)
    {
        $this->facebookUid = $facebookUid;

        return $this;
    }

    public function getFacebookUid()
    {
        return $this->facebookUid;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setGplusData($gplusData)
    {
        $this->gplusData = $gplusData;

        return $this;
    }

    public function getGplusData()
    {
        return $this->gplusData;
    }

    public function setGplusName($gplusName)
    {
        $this->gplusName = $gplusName;

        return $this;
    }

    public function getGplusName()
    {
        return $this->gplusName;
    }

    public function setGplusUid($gplusUid)
    {
        $this->gplusUid = $gplusUid;

        return $this;
    }

    public function getGplusUid()
    {
        return $this->gplusUid;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTwitterData($twitterData)
    {
        $this->twitterData = $twitterData;

        return $this;
    }

    public function getTwitterData()
    {
        return $this->twitterData;
    }

    public function setTwitterName($twitterName)
    {
        $this->twitterName = $twitterName;

        return $this;
    }

    public function getTwitterName()
    {
        return $this->twitterName;
    }

    public function setTwitterUid($twitterUid)
    {
        $this->twitterUid = $twitterUid;

        return $this;
    }

    public function getTwitterUid()
    {
        return $this->twitterUid;
    }

    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function getFullname()
    {
        return sprintf('%s %s', $this->getFirstname(), $this->getLastname());
    }

    public function getRealRoles()
    {
        return $this->roles;
    }

    public function setRealRoles(array $roles)
    {
        $this->setRoles($roles);

        return $this;
    }

    /**
     * Returns the gender list.
     *
     * @return array
     */
    public static function getGenderList()
    {
        return [
            'gender_unknown' => UserInterface::GENDER_UNKNOWN,
            'gender_female' => UserInterface::GENDER_FEMALE,
            'gender_male' => UserInterface::GENDER_MALE,
        ];
    }
}
