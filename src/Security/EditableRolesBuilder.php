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

use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EditableRolesBuilder
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $rolesHierarchy;

    /**
     * @var array
     */
    private $labelPermission;

    /**
     * @var array
     */
    private $labelAdmin;

    /**
     * @var array
     */
    private $exclude;

    /**
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param Pool                          $pool
     * @param array                         $rolesHierarchy
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, Pool $pool, array $rolesHierarchy = [])
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->pool = $pool;
        $this->rolesHierarchy = $rolesHierarchy;
        $this->labelPermission = [];
        $this->labelAdmin = [];
        $this->exclude = [];
    }

    /*
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param string|bool|null $domain
     * @param bool             $expanded
     *
     * @return array
     */
    public function getRoles($domain = false, $expanded = true)
    {
        $roles = [];

        if (!$this->tokenStorage->getToken()) {
            return $roles;
        }

        $this->iterateAdminRoles(function ($role, $isMaster) use ($domain, &$roles): void {
            if ($isMaster) {
                $roles[$role] = $this->translateRole($role, $domain);
            }
        });

        $roleSuperAdmin = $this->pool->getOption('role_super_admin');
        $roleSonataAdmin = $this->pool->getOption('role_admin');

        $adminRoles = [
            $roleSuperAdmin,
            'ROLE_ADMIN',
            $roleSonataAdmin,
        ];
        $roles['other'] = array_combine($adminRoles, $adminRoles);
        $roles['other'] = array_filter($roles['other']);

        // get roles from the service container
        foreach ($this->rolesHierarchy as $name => $rolesHierarchy) {
            $roles['other'][$name] = $this->translateRole($name, $domain);
            if ($expanded) {
                $result = array_map([$this, 'translateRole'], $rolesHierarchy, array_fill(0, count($rolesHierarchy), $domain));
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
            throw new \InvalidArgumentException('You must add this line in the configuration of Sonata Admin: "[security: handler: sonata.admin.security.handler.role]"');
        }

        return $roles;
    }

    /**
     * @param string|bool|null $domain
     *
     * @return array
     */
    public function getRolesReadOnly($domain = false)
    {
        $rolesReadOnly = [];

        if (!$this->tokenStorage->getToken()) {
            return $rolesReadOnly;
        }

        $this->iterateAdminRoles(function ($role, $isMaster) use ($domain, &$rolesReadOnly): void {
            if (!$isMaster) {
                $rolesReadOnly[$role] = $role;
            }
        });

        return $rolesReadOnly;
    }

    /**
     * @return array
     */
    final public function getExclude()
    {
        return $this->exclude;
    }

    /**
     * @param string $exclude
     */
    final public function addExclude($exclude): void
    {
        $this->exclude[] = $exclude;
    }

    /**
     * @return array
     */
    final public function getLabelPermission()
    {
        return $this->labelPermission;
    }

    /**
     * @return array
     */
    final public function getLabelAdmin()
    {
        return $this->labelAdmin;
    }

    private function iterateAdminRoles(callable $func): void
    {
        // get roles from the Admin classes
        foreach ($this->pool->getAdminServiceIds() as $id) {
            try {
                $admin = $this->pool->getInstance($id);
            } catch (\Exception $e) {
                continue;
            }

            if (in_array($id, $this->exclude)) {
                continue;
            }

            $isMaster = ($admin->isGranted('MASTER') || $admin->isGranted('OPERATOR') ||
                $this->authorizationChecker->isGranted($this->pool->getOption('role_super_admin')))
            ;

            $securityHandler = $admin->getSecurityHandler();
            // TODO get the base role from the admin or security handler
            $baseRole = $securityHandler->getBaseRole($admin);
            $groupPermission = $admin->getSecurityInformation();
            $this->labelPermission = array_keys($groupPermission);
            $this->labelAdmin[] = $admin->trans($admin->getLabel());

            if (0 == strlen($baseRole)) { // the security handler related to the admin does not provide a valid string
                continue;
            }

            foreach ($groupPermission as $role => $permissions) {
                $role = sprintf($baseRole, $role);
                call_user_func($func, $role, $isMaster, $permissions);
            }
        }
    }

    /*
     * @param string $role
     * @param string|bool|null $domain
     *
     * @return string
     */
    private function translateRole($role, $domain)
    {
        // translation domain is false, do not translate it,
        // null is fallback to message domain
        if (false === $domain || !isset($this->translator)) {
            return $role;
        }

        return $this->translator->trans($role, [], $domain);
    }

    /**
     * @param string $role
     * @param array  $roles
     *
     * @return bool
     */
    private function recursiveArraySearch($role, array $roles)
    {
        foreach ($roles as $key => $value) {
            if ($role === $key || (is_array($value) && true === $this->recursiveArraySearch($role, $value))) {
                return true;
            }
        }

        return false;
    }
}
