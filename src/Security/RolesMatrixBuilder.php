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

namespace Sonata\UserBundle\Security;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class RolesMatrixBuilder implements RolesBuilderInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

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

    /**
     * @var string []
     */
    private $excludeAdmin = [];

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        Pool $pool,
        array $rolesHierarchy = []
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->pool = $pool;
        $this->rolesHierarchy = $rolesHierarchy;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function getRoles(string $domain = null, bool $expanded = true): array
    {
        if (!$this->tokenStorage->getToken()) {
            return [];
        }

        $adminRoles = $this->getAdminRoles($domain);
        $securityRoles = $this->getSecurityRoles($this->rolesHierarchy, $domain, $expanded);

        return array_merge($securityRoles, $adminRoles);
    }

    public function getPermissionLabels(): array
    {
        $permissionLabels = [];
        foreach ($this->getRoles() as $role => $attributes) {
            if (isset($attributes['label'])) {
                $permissionLabels[$attributes['label']] = $attributes['label'];
            }
        }

        return $permissionLabels;
    }

    public function getExcludeAdmin(): array
    {
        return $this->excludeAdmin;
    }

    public function addExcludeAdmin(string $exclude): void
    {
        $this->excludeAdmin[] = $exclude;
    }

    private function getSecurityRoles(array $hierarchy, string $domain = null, bool $expanded = true): array
    {
        $baseRoles = [$this->pool->getOption('role_super_admin'), $this->pool->getOption('role_admin')];
        $baseRoles = array_combine($baseRoles, $baseRoles);
        $hierarchy = array_merge($baseRoles, $hierarchy);

        $securityRoles = [];
        foreach ($hierarchy as $role => $childRoles) {
            $securityRoles[$role] = $this->translateRole($role, $domain);
            if ($expanded) {
                $concatedRole = array_map([$this, 'translateRole'], $childRoles,
                    array_fill(0, count($childRoles), $domain));

                $securityRoles[$role] = [
                    'role' => $role,
                    'role_translated' => $role.': '.implode(', ', $concatedRole),
                    'is_granted' => $this->authorizationChecker->isGranted($role),
                ];
            }

            foreach ($childRoles as $role) {
                if (!array_key_exists($role, $hierarchy) && !isset($securityRoles[$role])
                    && !$this->recursiveArraySearch($role, $securityRoles)) {
                    $securityRoles[$role] = [
                        'role' => $role,
                        'role_translated' => $this->translateRole($role, $domain),
                        'is_granted' => $this->authorizationChecker->isGranted($role),
                    ];
                }
            }
        }

        return $securityRoles;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function getAdminRoles(string $domain = null): array
    {
        $adminRoles = [];
        foreach ($this->pool->getAdminServiceIds() as $id) {
            if (in_array($id, $this->excludeAdmin)) {
                continue;
            }

            $admin = $this->pool->getInstance($id);
            $securityHandler = $admin->getSecurityHandler();
            $baseRole = $securityHandler->getBaseRole($admin);
            foreach ($admin->getSecurityInformation() as $key => $permission) {
                $role = sprintf($baseRole, $key);
                $adminRoles[$role] = [
                    'role' => $role,
                    'label' => $key,
                    'role_translated' => $this->translateRole($role, $domain),
                    'is_granted' => $this->isMaster($admin) || $this->authorizationChecker->isGranted($role),
                    'admin_label' => $admin->getTranslator()->trans($admin->getLabel()),
                ];
            }
        }

        return $adminRoles;
    }

    private function isMaster(AdminInterface $admin): bool
    {
        return $admin->isGranted('MASTER') || $admin->isGranted('OPERATOR')
            || $this->authorizationChecker->isGranted($this->pool->getOption('role_super_admin'));
    }

    private function translateRole(string $role, $domain): string
    {
        // translation domain is false, do not translate it,
        // null is fallback to message domain
        if (false === $domain || !isset($this->translator)) {
            return $role;
        }

        return $this->translator->trans($role, [], $domain);
    }

    private function recursiveArraySearch(string $role, array $roles): bool
    {
        foreach ($roles as $key => $value) {
            if ($role === $key || (is_array($value) && true === $this->recursiveArraySearch($role, $value))) {
                return true;
            }
        }

        return false;
    }
}
