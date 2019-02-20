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

use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class SecurityRolesBuilder implements ExpandableRolesBuilderInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string []
     */
    private $rolesHierarchy;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        Pool $pool,
        TranslatorInterface $translator,
        array $rolesHierarchy = []
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->pool = $pool;
        $this->translator = $translator;
        $this->rolesHierarchy = $rolesHierarchy;
    }

    public function getExpandedRoles(?string $domain = null): array
    {
        $securityRoles = [];
        foreach ($hierarchy = $this->getHierarchy() as $role => $childRoles) {
            $translatedRoles = array_map(
                [$this, 'translateRole'],
                $childRoles,
                array_fill(0, \count($childRoles), $domain)
            );

            $translatedRoles = \count($translatedRoles) > 0 ? ': '.implode(', ', $translatedRoles) : '';
            $securityRoles[$role] = [
                'role' => $role,
                'role_translated' => $role.$translatedRoles,
                'is_granted' => $this->authorizationChecker->isGranted($role),
            ];

            $securityRoles = array_merge(
                $securityRoles,
                $this->getSecurityRoles($hierarchy, $childRoles, $domain)
            );
        }

        return $securityRoles;
    }

    public function getRoles(?string $domain = null): array
    {
        $securityRoles = [];
        foreach ($hierarchy = $this->getHierarchy() as $role => $childRoles) {
            $securityRoles[$role] = $this->getSecurityRole($role, $domain);
            $securityRoles = array_merge(
                $securityRoles,
                $this->getSecurityRoles($hierarchy, $childRoles, $domain)
            );
        }

        return $securityRoles;
    }

    private function getHierarchy(): array
    {
        return array_merge([
            $this->pool->getOption('role_super_admin') => [],
            $this->pool->getOption('role_admin') => [],
        ], $this->rolesHierarchy);
    }

    private function getSecurityRole(string $role, ?string $domain): array
    {
        return [
            'role' => $role,
            'role_translated' => $this->translateRole($role, $domain),
            'is_granted' => $this->authorizationChecker->isGranted($role),
        ];
    }

    private function getSecurityRoles(array $hierarchy, array $roles, ?string $domain): array
    {
        $securityRoles = [];
        foreach ($roles as $role) {
            if (!\array_key_exists($role, $hierarchy) && !isset($securityRoles[$role])
                && !$this->recursiveArraySearch($role, $securityRoles)) {
                $securityRoles[$role] = $this->getSecurityRole($role, $domain);
            }
        }

        return $securityRoles;
    }

    private function translateRole(string $role, $domain): string
    {
        if ($domain) {
            return $this->translator->trans($role, [], $domain);
        }

        return $role;
    }

    private function recursiveArraySearch(string $role, array $roles): bool
    {
        foreach ($roles as $key => $value) {
            if ($role === $key || (\is_array($value) && true === $this->recursiveArraySearch($role, $value))) {
                return true;
            }
        }

        return false;
    }
}
