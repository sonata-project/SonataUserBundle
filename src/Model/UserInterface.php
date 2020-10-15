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

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

interface UserInterface extends AdvancedUserInterface, \Serializable
{
    public const GENDER_FEMALE = 'f';
    public const GENDER_MALE = 'm';
    public const GENDER_UNKNOWN = 'u';

    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * Returns the user unique id.
     *
     * @return mixed
     */
    public function getId();

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
     * Sets the username.
     *
     * @param string $username
     *
     * @return UserInterface
     */
    public function setUsername($username);

    /**
     * Gets the canonical username in search and sort queries.
     *
     * @return string
     */
    public function getUsernameCanonical();

    /**
     * Sets the canonical username.
     *
     * @param string $usernameCanonical
     *
     * @return static
     */
    public function setUsernameCanonical($usernameCanonical);

    /**
     * @param string|null $salt
     *
     * @return static
     */
    public function setSalt($salt);

    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Sets the email.
     *
     * @param string $email
     *
     * @return static
     */
    public function setEmail($email);

    /**
     * Gets the canonical email in search and sort queries.
     *
     * @return string
     */
    public function getEmailCanonical();

    /**
     * Sets the canonical email.
     *
     * @param string $emailCanonical
     *
     * @return static
     */
    public function setEmailCanonical($emailCanonical);

    /**
     * Gets the plain password.
     *
     * @return string
     */
    public function getPlainPassword();

    /**
     * Sets the plain password.
     *
     * @param string $password
     *
     * @return static
     */
    public function setPlainPassword($password);

    /**
     * Sets the hashed password.
     *
     * @param string $password
     *
     * @return static
     */
    public function setPassword($password);

    /**
     * Tells if the the given user has the super admin role.
     *
     * @return bool
     */
    public function isSuperAdmin();

    /**
     * @param bool $boolean
     *
     * @return static
     */
    public function setEnabled($boolean);

    /**
     * Sets the super admin status.
     *
     * @param bool $boolean
     *
     * @return static
     */
    public function setSuperAdmin($boolean);

    /**
     * Gets the confirmation token.
     *
     * @return string|null
     */
    public function getConfirmationToken();

    /**
     * Sets the confirmation token.
     *
     * @param string|null $confirmationToken
     *
     * @return static
     */
    public function setConfirmationToken($confirmationToken);

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @return static
     */
    public function setPasswordRequestedAt(?\DateTime $date = null);

    /**
     * Checks whether the password reset request has expired.
     *
     * @param int $ttl Requests older than this many seconds will be considered expired
     *
     * @return bool
     */
    public function isPasswordRequestNonExpired($ttl);

    /**
     * Sets the last login time.
     *
     * @return static
     */
    public function setLastLogin(?\DateTime $time = null);

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $authorizationChecker->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role);

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @return static
     */
    public function setRoles(array $roles);

    /**
     * Adds a role to the user.
     *
     * @param string $role
     *
     * @return static
     */
    public function addRole($role);

    /**
     * Removes a role to the user.
     *
     * @param string $role
     *
     * @return static
     */
    public function removeRole($role);

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
     * Sets the two-step verification code.
     *
     * @param string $twoStepVerificationCode
     *
     * @return UserInterface
     */
    public function setTwoStepVerificationCode($twoStepVerificationCode);

    /**
     * Returns the two-step verification code.
     *
     * @return string
     */
    public function getTwoStepVerificationCode();

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
     * @param string $token
     *
     * @return UserInterface
     */
    public function setToken($token);

    /**
     * @return string
     */
    public function getToken();

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
