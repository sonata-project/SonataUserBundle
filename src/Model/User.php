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
     * @see AbstractedUser::serialize()
     */
    public function __serialize()
    {
        return serialize([
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical,
        ]);
    }

    /**
     * @see AbstractedUser::unserialize()
     */
    public function __unserialize($serialized)
    {
        $data = unserialize($serialized);

        if (13 === \count($data)) {
            // Unserializing a User object from 1.3.x
            unset($data[4], $data[5], $data[6], $data[9], $data[10]);
            $data = array_values($data);
        } elseif (11 === \count($data)) {
            // Unserializing a User from a dev version somewhere between 2.0-alpha3 and 2.0-beta1
            unset($data[4], $data[7], $data[8]);
            $data = array_values($data);
        }

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

    public function setTwoStepVerificationCode($twoStepVerificationCode)
    {
        $this->twoStepVerificationCode = $twoStepVerificationCode;

        return $this;
    }

    public function getTwoStepVerificationCode()
    {
        return $this->twoStepVerificationCode;
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

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
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
