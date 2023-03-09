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

use Sonata\AdminBundle\SonataConfiguration;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 *
 * @phpstan-import-type Role from RolesBuilderInterface
 */
final class SecurityRolesBuilder implements ExpandableRolesBuilderInterface
{
    /**
     * @param array<string, array<string>> $rolesHierarchy
     */
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
        private SonataConfiguration $configuration,
        private TranslatorInterface $translator,
        private array $rolesHierarchy = []
    ) {
    }

    public function getExpandedRoles(?string $domain = null): array
    {
        $securityRoles = [];
        $hierarchy = $this->getHierarchy();

        foreach ($hierarchy as $role => $childRoles) {
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
        $hierarchy = $this->getHierarchy();

        foreach ($hierarchy as $role => $childRoles) {
            $securityRoles[$role] = $this->getSecurityRole($role, $domain);
            $securityRoles = array_merge(
                $securityRoles,
                $this->getSecurityRoles($hierarchy, $childRoles, $domain)
            );
        }

        return $securityRoles;
    }

    /**
     * @return array<string, array<string>>
     */
    private function getHierarchy(): array
    {
        $roleSuperAdmin = $this->configuration->getOption('role_super_admin');
        \assert(\is_string($roleSuperAdmin));

        $roleAdmin = $this->configuration->getOption('role_admin');
        \assert(\is_string($roleAdmin));

        return array_merge([
            $roleSuperAdmin => [],
            $roleAdmin => [],
        ], $this->rolesHierarchy);
    }

    /**
     * @return array<string, string|bool>
     *
     * @phpstan-return Role
     */
    private function getSecurityRole(string $role, ?string $domain): array
    {
        return [
            'role' => $role,
            'role_translated' => $this->translateRole($role, $domain),
            'is_granted' => $this->authorizationChecker->isGranted($role),
        ];
    }

    /**
     * @param string[][] $hierarchy
     * @param string[]   $roles
     *
     * @return array<string, array<string, string|bool>>
     *
     * @phpstan-return Role[]
     */
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

    private function translateRole(string $role, ?string $domain): string
    {
        if (null !== $domain) {
            return $this->translator->trans($role, [], $domain);
        }

        return $role;
    }

    /**
     * @param array<string, array<string, string|bool>>|array<string, string|bool> $roles
     *
     * @phpstan-param Role[]|Role $roles
     */
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
