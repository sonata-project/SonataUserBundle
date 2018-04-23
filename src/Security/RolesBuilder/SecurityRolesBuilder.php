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
final class SecurityRolesBuilder implements RolesBuilderInterface
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

    public function getPermissionLabels(): array
    {
        return [];
    }

    public function getRoles(string $domain = null, bool $expanded = true): array
    {
        $baseRoles = [$this->pool->getOption('role_super_admin') => [],
            $this->pool->getOption('role_admin') => [], ];
        $hierarchy = array_merge($baseRoles, $this->rolesHierarchy);

        $securityRoles = [];
        foreach ($hierarchy as $role => $childRoles) {
            $securityRoles[$role] = [
                'role' => $role,
                'role_translated' => $this->translateRole($role, $domain),
                'is_granted' => $this->authorizationChecker->isGranted($role),
            ];

            if ($expanded) {
                $translatedRoles = array_map(
                    [$this, 'translateRole'],
                    $childRoles,
                    array_fill(0, count($childRoles), $domain)
                );

                $securityRoles[$role] = [
                    'role' => $role,
                    'role_translated' => $role.': '.implode(', ', $translatedRoles),
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
            if ($role === $key || (is_array($value) && true === $this->recursiveArraySearch($role, $value))) {
                return true;
            }
        }

        return false;
    }
}
