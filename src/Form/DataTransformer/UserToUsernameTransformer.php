<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Form\DataTransformer;

use Sonata\UserBundle\Model\FOSUserInterface;
use Sonata\UserBundle\Model\FOSUserManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms between a FOSUserInterface instance and a username string.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class UserToUsernameTransformer implements DataTransformerInterface
{
    /**
     * @var FOSUserManagerInterface
     */
    protected $userManager;

    /**
     * UserToUsernameTransformer constructor.
     *
     * @param FOSUserManagerInterface $userManager
     */
    public function __construct(FOSUserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Transforms a FOSUserInterface instance into a username string.
     *
     * @param FOSUserInterface|null $value FOSUserInterface instance
     *
     * @return string|null Username
     *
     * @throws UnexpectedTypeException if the given value is not a FOSUserInterface instance
     */
    public function transform($value)
    {
        if (null === $value) {
            return;
        }

        if (!$value instanceof FOSUserInterface) {
            throw new UnexpectedTypeException($value, 'Sonata\UserBundle\Model\FOSUserInterface');
        }

        return $value->getUsername();
    }

    /**
     * Transforms a username string into a FOSUserInterface instance.
     *
     * @param string $value Username
     *
     * @return FOSUserInterface the corresponding FOSUserInterface instance
     *
     * @throws UnexpectedTypeException if the given value is not a string
     */
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        return $this->userManager->findUserByUsername($value);
    }
}
