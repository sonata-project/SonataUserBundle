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

namespace Sonata\UserBundle\Form\Transformer;

use Sonata\UserBundle\Security\EditableRolesBuilder;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @phpstan-implements DataTransformerInterface<string[], string[]>
 */
class RestoreRolesTransformer implements DataTransformerInterface
{
    /**
     * @var EditableRolesBuilder
     */
    protected $rolesBuilder;

    /**
     * @var string[]|null
     */
    protected $originalRoles = null;

    public function __construct(EditableRolesBuilder $rolesBuilder)
    {
        $this->rolesBuilder = $rolesBuilder;
    }

    /**
     * @param string[]|null $originalRoles
     */
    public function setOriginalRoles(?array $originalRoles = null): void
    {
        $this->originalRoles = $originalRoles ?? [];
    }

    /**
     * @return string[]|null
     */
    #[\ReturnTypeWillChange]
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (null === $this->originalRoles) {
            throw new \RuntimeException('Invalid state, originalRoles array is not set');
        }

        return $value;
    }

    /**
     * @return string[]|null
     */
    #[\ReturnTypeWillChange]
    public function reverseTransform($selectedRoles)
    {
        if (null === $this->originalRoles) {
            throw new \RuntimeException('Invalid state, originalRoles array is not set');
        }

        $availableRoles = $this->rolesBuilder->getRoles();

        $hiddenRoles = array_diff($this->originalRoles, array_keys($availableRoles));

        return array_merge($selectedRoles ?? [], $hiddenRoles);
    }
}
