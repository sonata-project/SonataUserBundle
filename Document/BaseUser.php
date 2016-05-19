<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Document;

use FOS\UserBundle\Document\User as AbstractedUser;
use Sonata\UserBundle\Model\UserInterface;

/**
 * Represents a Base User Document.
 */
class BaseUser extends AbstractedUser implements UserInterface
{
    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $twoStepVerificationCode;

    /**
     * @var \DateTime
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
     * Sets the creation date.
     *
     * @param \DateTime|null $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns the creation date.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets the last update date.
     *
     * @param \DateTime|null $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns the last update date.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set expiration date.
     *
     * @param \DateTime|null $date
     *
     * @return $this
     */
    public function setExpiresAt(\DateTime $date = null)
    {
        $this->expiresAt = $date;

        return $this;
    }

    /**
     * Returns the expiration date.
     *
     * @return \DateTime|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Sets the credentials expiration date.
     *
     * @param \DateTime|null $date
     *
     * @return $this
     */
    public function setCredentialsExpireAt(\DateTime $date = null)
    {
        $this->credentialsExpireAt = $date;

        return $this;
    }

    /**
     * Returns the credentials expiration date.
     *
     * @return \DateTime
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }

    /**
     * Sets the user groups.
     *
     * @param array $groups
     *
     * @return $this
     */
    public function setGroups($groups)
    {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }

        return $this;
    }

    /**
     * Sets the two-step verification code.
     *
     * @param string $twoStepVerificationCode
     *
     * @return $this
     */
    public function setTwoStepVerificationCode($twoStepVerificationCode)
    {
        $this->twoStepVerificationCode = $twoStepVerificationCode;

        return $this;
    }

    /**
     * Returns the two-step verification code.
     *
     * @return string
     */
    public function getTwoStepVerificationCode()
    {
        return $this->twoStepVerificationCode;
    }

    /**
     * @param string $biography
     *
     * @return $this
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;

        return $this;
    }

    /**
     * @return string
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * @param \DateTime $dateOfBirth
     *
     * @return $this
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param string $facebookData
     *
     * @return $this
     */
    public function setFacebookData($facebookData)
    {
        $this->facebookData = $facebookData;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookData()
    {
        return $this->facebookData;
    }

    /**
     * @param string $facebookName
     *
     * @return $this
     */
    public function setFacebookName($facebookName)
    {
        $this->facebookName = $facebookName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookName()
    {
        return $this->facebookName;
    }

    /**
     * @param string $facebookUid
     *
     * @return $this
     */
    public function setFacebookUid($facebookUid)
    {
        $this->facebookUid = $facebookUid;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookUid()
    {
        return $this->facebookUid;
    }

    /**
     * @param string $firstname
     *
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $gender
     *
     * @return $this
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gplusData
     *
     * @return $this
     */
    public function setGplusData($gplusData)
    {
        $this->gplusData = $gplusData;

        return $this;
    }

    /**
     * @return string
     */
    public function getGplusData()
    {
        return $this->gplusData;
    }

    /**
     * @param string $gplusName
     *
     * @return $this
     */
    public function setGplusName($gplusName)
    {
        $this->gplusName = $gplusName;

        return $this;
    }

    /**
     * @return string
     */
    public function getGplusName()
    {
        return $this->gplusName;
    }

    /**
     * @param string $gplusUid
     *
     * @return $this
     */
    public function setGplusUid($gplusUid)
    {
        $this->gplusUid = $gplusUid;

        return $this;
    }

    /**
     * @return string
     */
    public function getGplusUid()
    {
        return $this->gplusUid;
    }

    /**
     * @param string $lastname
     *
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $timezone
     *
     * @return $this
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $twitterData
     *
     * @return $this
     */
    public function setTwitterData($twitterData)
    {
        $this->twitterData = $twitterData;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterData()
    {
        return $this->twitterData;
    }

    /**
     * @param string $twitterName
     *
     * @return $this
     */
    public function setTwitterName($twitterName)
    {
        $this->twitterName = $twitterName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterName()
    {
        return $this->twitterName;
    }

    /**
     * @param string $twitterUid
     *
     * @return $this
     */
    public function setTwitterUid($twitterUid)
    {
        $this->twitterUid = $twitterUid;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterUid()
    {
        return $this->twitterUid;
    }

    /**
     * @param string $website
     *
     * @return $this
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return sprintf('%s %s', $this->getFirstname(), $this->getLastname());
    }

    /**
     * @return array
     */
    public function getRealRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRealRoles(array $roles)
    {
        $this->setRoles($roles);

        return $this;
    }

    /**
     * Hook on pre-persist operations.
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Hook on pre-update operations.
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Returns the gender list.
     *
     * @return array
     */
    public static function getGenderList()
    {
        return array(
            UserInterface::GENDER_UNKNOWN => 'gender_unknown',
            UserInterface::GENDER_FEMALE => 'gender_female',
            UserInterface::GENDER_MALE => 'gender_male',
        );
    }
}
