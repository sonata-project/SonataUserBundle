<?php

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
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\SecurityContextInterface;

class EditableRolesBuilder
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

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
     * @param SecurityContextInterface $securityContext
     * @param Pool                     $pool
     * @param array                    $rolesHierarchy
     */
    public function __construct(SecurityContextInterface $securityContext, Pool $pool, array $rolesHierarchy = array())
    {
        $this->securityContext = $securityContext;
        $this->pool = $pool;
        $this->rolesHierarchy = $rolesHierarchy;
        $this->labelPermission = array();
        $this->labelAdmin = array();
        $this->exclude = array();
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
    final public function addExclude($exclude)
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
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getRoles()
    {
        $roles = array();
        $rolesReadOnly = array();

        if (!$this->securityContext->getToken()) {
            return array($roles, $rolesReadOnly);
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
                $this->securityContext->isGranted('ROLE_SUPER_ADMIN') ||
                $this->securityContext->isGranted('ROLE_SONATA_ADMIN'));
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

        $roles['other'] = array(
            'ROLE_ADMIN' => 'Role Admin',
            'ROLE_SUPER_ADMIN' => 'Role Super Admin',
            'ROLE_SONATA_ADMIN' => 'Role Sonata Admin',
        );

        foreach ($this->rolesHierarchy as $name => $rolesHierarchy) {
            if ($this->securityContext->isGranted($name) || $isMaster) {
                foreach ($rolesHierarchy as $role) {
                    if (array_key_exists($role, $this->rolesHierarchy) === false && !isset($roles['other'][$role]) && $this->recursiveArraySearch($role, $roles) === false) {
                        $roles['other'][$role] = ucfirst(strtolower(str_replace('_', ' ', $role)));
                    }
                }
            }
        }

        if (empty($this->labelPermission)) {
            throw new \InvalidArgumentException('You must add this line in the configuration of Sonata Admin: '.
                "[security:\n\thandler: sonata.admin.security.handler.role]");
        }

        return array($roles, $rolesReadOnly);
    }

    /**
     * @param $needle
     * @param array $haystack
     *
     * @return bool
     */
    private function recursiveArraySearch($needle, array $haystack)
    {
        foreach ($haystack as $key => $value) {
            if ($needle === $key || (is_array($value) && $this->recursiveArraySearch($needle, $value) === true)) {
                return true;
            }
        }

        return false;
    }
}
