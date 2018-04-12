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

use Psr\Log\LoggerInterface;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class RolesMatrixBuilder implements RolesBuilderInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';

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
     * @var array
     */
    private $rolesHierarchy;

    /**
     * @var array
     */
    private $labelPermission = [];

    /**
     * @var array
     */
    private $labelAdmin = [];

    /**
     * @var array
     */
    private $exclude = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        Pool $pool,
        LoggerInterface $logger,
        array $rolesHierarchy = []
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->pool = $pool;
        $this->rolesHierarchy = $rolesHierarchy;
        $this->logger = $logger;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param string|bool|null $domain
     */
    public function getRoles($domain = false, bool $expanded = true): array
    {
        $roles = [];

        if (!$this->tokenStorage->getToken()) {
            return $roles;
        }

        $this->iterateAdminRoles(function ($role, $isMaster) use ($domain, &$roles): void {
            $roles[$role] = $this->translateRole($role, $domain);
        });

        $roleSuperAdmin = $this->pool->getOption('role_super_admin');
        $roleSonataAdmin = $this->pool->getOption('role_admin');

        $baseRoles = [
            $roleSuperAdmin,
            self::ROLE_ADMIN,
            $roleSonataAdmin,
        ];
        $roles['other'] = array_combine($baseRoles, $baseRoles);
        $roles['other'] = array_filter($roles['other']);

        // get roles from the service container
        foreach ($this->rolesHierarchy as $name => $rolesHierarchy) {
            $roles['other'][$name] = $this->translateRole($name, $domain);
            if ($expanded) {
                $result = array_map(
                    [$this, 'translateRole'],
                    $rolesHierarchy,
                    array_fill(0, count($rolesHierarchy), $domain)
                );
                $roles['other'][$name] .= ': '.implode(', ', $result);
            }

            foreach ($rolesHierarchy as $role) {
                if (false === array_key_exists($role, $this->rolesHierarchy)
                    && !isset($roles['other'][$role])
                    && false === $this->recursiveArraySearch($role, $roles)
                ) {
                    $roles['other'][$role] = $this->translateRole($role, $domain);
                }
            }
        }

        $permittedRoles = [];
        foreach ($roles['other'] as $key => $role) {
            if ($this->authorizationChecker->isGranted($key)) {
                $permittedRoles['other'][$role] = $key;
            } else {
                $permittedRoles['hidden'][$role] = $key;
            }
        }

        unset($roles['other']);
        $roles = array_merge($roles, $permittedRoles);

        if (empty($this->labelPermission)) {
            throw new \InvalidArgumentException(
                'You must add this line in the configuration of Sonata Admin:'
                .'"[security: handler: sonata.admin.security.handler.role]"'
            );
        }

        return $roles;
    }

    public function getCustomRolesForView(): array
    {
        $roles = [];

        // get roles from the service container
        foreach ($this->rolesHierarchy as $name => $rolesHierarchy) {
            $roles[$name] = [
                'read_only' => !$this->authorizationChecker->isGranted($name),
            ];

            foreach ($rolesHierarchy as $role) {
                if (false === array_key_exists($role, $this->rolesHierarchy)
                    && !isset($roles[$role])
                    && false === $this->recursiveArraySearch($role, $roles)
                ) {
                    $roles[$role] = [
                        'read_only' => !$this->authorizationChecker->isGranted($role),
                    ];
                }
            }
        }

        return $roles;
    }

    public function getExclude(): array
    {
        return $this->exclude;
    }

    public function addExclude(string $exclude): void
    {
        $this->exclude[] = $exclude;
    }

    public function getLabelPermission(): array
    {
        return $this->labelPermission;
    }

    public function getLabelAdmin(): array
    {
        return $this->labelAdmin;
    }

    public function getAdminRolesForView(): array
    {
        $roles = [];
        // get roles from the Admin classes
        foreach ($this->pool->getAdminServiceIds() as $id) {
            try {
                $admin = $this->pool->getInstance($id);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                continue;
            }

            if (in_array($id, $this->exclude)) {
                continue;
            }

            $isMaster = ($admin->isGranted('MASTER') || $admin->isGranted('OPERATOR') ||
                $this->authorizationChecker->isGranted($this->pool->getOption('role_super_admin')));

            $securityHandler = $admin->getSecurityHandler();
            $baseRole = $securityHandler->getBaseRole($admin);
            $groupPermission = $admin->getSecurityInformation();
            $this->labelPermission = array_keys($groupPermission);
            $this->labelAdmin[] = $admin->getTranslator()->trans($admin->getLabel());

            if (0 == strlen($baseRole)) { // the security handler related to the admin does not provide a valid string
                continue;
            }

            $roles[$baseRole] = [
                'label' => $admin->getTranslator()->trans($admin->getLabel()),
            ];

            foreach ($groupPermission as $name => $item) {
                $role = sprintf($baseRole, $name);
                $roles[$baseRole]['permissions'][$name] =
                    !$isMaster && !$this->authorizationChecker->isGranted($role);
            }
        }

        return $roles;
    }

    private function iterateAdminRoles(callable $func): void
    {
        // get roles from the Admin classes
        foreach ($this->pool->getAdminServiceIds() as $id) {
            try {
                $admin = $this->pool->getInstance($id);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                continue;
            }

            if (in_array($id, $this->exclude)) {
                continue;
            }

            $isMaster = ($admin->isGranted('MASTER') || $admin->isGranted('OPERATOR') ||
                $this->authorizationChecker->isGranted($this->pool->getOption('role_super_admin')))
            ;

            $securityHandler = $admin->getSecurityHandler();
            $baseRole = $securityHandler->getBaseRole($admin);
            $groupPermission = $admin->getSecurityInformation();
            $this->labelPermission = array_keys($groupPermission);

            $this->labelAdmin[] = $admin->getTranslator()->trans($admin->getLabel());

            if (0 == strlen($baseRole)) { // the security handler related to the admin does not provide a valid string
                continue;
            }

            foreach ($groupPermission as $role => $permissions) {
                $role = sprintf($baseRole, $role);
                call_user_func($func, $role, $isMaster, $permissions);
            }
        }
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
