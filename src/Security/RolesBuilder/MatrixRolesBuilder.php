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

namespace Sonata\UserBundle\Security\RolesBuilder;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sonata\UserBundle\Security\RolesBuilder\ExpandableRolesBuilderInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class MatrixRolesBuilder implements ExpandableRolesBuilderInterface, PermissionLabelsBuilderInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RolesBuilderInterface
     */
    private $adminRolesBuilder;

    /**
     * @var RolesBuilderInterface
     */
    private $securityRolesBuilder;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        ExpandableRolesBuilderInterface $adminRolesBuilder,
        ExpandableRolesBuilderInterface $securityRolesBuilder
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->adminRolesBuilder = $adminRolesBuilder;
        $this->securityRolesBuilder = $securityRolesBuilder;
    }

    public function getRoles(string $domain = null, bool $expanded = true): array
    {
        if (!$this->tokenStorage->getToken()) {
            return [];
        }

        return array_merge(
            $this->securityRolesBuilder->getRoles($domain),
            $this->adminRolesBuilder->getRoles($domain, $expanded)
        );
    }

    public function getPermissionLabels(): array
    {
        return $this->adminRolesBuilder->getPermissionLabels();
    }
}
