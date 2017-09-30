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
use Symfony\Component\Security\Core\SecurityContextInterface;

class EditableRolesBuilder
{
    /**
     * @var TokenStorageInterface|SecurityContextInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface|SecurityContextInterface
     */
    protected $authorizationChecker;

    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @var array
     */
    protected $rolesHierarchy;

    /**
     * @var array
     */
    protected $labelPermission;

    /**
     * @var array
     */
    protected $labelAdmin;

    /**
     * @var array
     */
    protected $exclude;

    /**
     * NEXT_MAJOR: Go back to type hinting check when bumping requirements to SF 2.6+.
     *
     * @param TokenStorageInterface|SecurityContextInterface         $tokenStorage
     * @param AuthorizationCheckerInterface|SecurityContextInterface $authorizationChecker
     * @param Pool                                                   $pool
     * @param array                                                  $rolesHierarchy
     */
    public function __construct($tokenStorage, $authorizationChecker, Pool $pool, array $rolesHierarchy = [])
    {
        if (!$tokenStorage instanceof TokenStorageInterface && !$tokenStorage instanceof SecurityContextInterface) {
            throw new \InvalidArgumentException(
                'Argument 1 should be an instance of Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface or Symfony\Component\Security\Core\SecurityContextInterface'
            );
        }
        if (!$authorizationChecker instanceof AuthorizationCheckerInterface && !$authorizationChecker instanceof SecurityContextInterface) {
            throw new \InvalidArgumentException(
                'Argument 2 should be an instance of Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface or Symfony\Component\Security\Core\SecurityContextInterface'
            );
        }

        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->pool = $pool;
        $this->rolesHierarchy = $rolesHierarchy;
        $this->labelPermission = [];
        $this->labelAdmin = [];
        $this->exclude = [];
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = [];
        $rolesReadOnly = [];

        if (!$this->tokenStorage->getToken()) {
            return [$roles, $rolesReadOnly];
        }

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
                $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') ||
                $this->authorizationChecker->isGranted('ROLE_SONATA_ADMIN'));
            $securityHandler = $admin->getSecurityHandler();
            $baseRole = $securityHandler->getBaseRole($admin);
            $groupPermission = $admin->getSecurityInformation();
            $this->labelPermission = array_keys($groupPermission);
            $this->labelAdmin[] = $admin->trans($admin->getLabel());

            foreach ($groupPermission as $role => $permissions) {
                $roles[str_replace('.', '_', $id)][sprintf($baseRole, $role)] = $role;
                if (!$isMaster) {
                    $rolesReadOnly[] = sprintf($baseRole, $role);
                }
            }
        }

        $roles['other'] = [
            'ROLE_ADMIN' => 'Role Admin',
            'ROLE_SUPER_ADMIN' => 'Role Super Admin',
            'ROLE_SONATA_ADMIN' => 'Role Sonata Admin',
        ];

        foreach ($this->rolesHierarchy as $name => $rolesHierarchy) {
            if ($this->authorizationChecker->isGranted($name) || $isMaster) {
                foreach ($rolesHierarchy as $role) {
                    if (false === array_key_exists($role, $this->rolesHierarchy)
                        && !isset($roles['other'][$role]) && false === $this->recursiveArraySearch($role, $roles)) {
                        $roles['other'][$role] = ucfirst(mb_strtolower(str_replace('_', ' ', $role)));
                    }
                }
            }
        }

        if (empty($this->labelPermission)) {
            throw new \InvalidArgumentException('You must add this line in the configuration of Sonata Admin: '.
                "[security:\n\thandler: sonata.admin.security.handler.role]");
        }

        return [$roles, $rolesReadOnly];
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
