<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Document;

use FOS\UserBundle\Document\User as AbstractedUser;
use Sonata\UserBundle\Model\UserInterface;

class BaseUser extends AbstractedUser implements UserInterface
{
    protected $createdAt;

    protected $updatedAt;

    protected $twoStepVerificationCode;

    /**
     * Set createdAt
     *
     * @param \DateTime|null $createdAt
     * @return void
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime|null $updatedAt
     * @return void
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return void
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    /**
     * @return void
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername() ?: '-';
    }

    /**
     * Set related groups
     *
     * @param aarrat $groups
     */
    public function setGroups($groups)
    {
        foreach ($groups as $group){
            $this->addGroup($group);
        }
    }

    /**
     * @param string $twoStepVerificationCode
     * @return void
     */
    public function setTwoStepVerificationCode($twoStepVerificationCode)
    {
        $this->twoStepVerificationCode = $twoStepVerificationCode;
    }

    /**
     * @return string
     */
    public function getTwoStepVerificationCode()
    {
        return $this->twoStepVerificationCode;
    }
}