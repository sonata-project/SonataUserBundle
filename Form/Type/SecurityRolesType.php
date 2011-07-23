<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\AdminBundle\Admin\Pool;

class SecurityRolesType extends ChoiceType
{
    protected $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    public function getDefaultOptions(array $options)
    {
        $options = parent::getDefaultOptions($options);

        $roles = array();
        if (count($options['choices']) == 0) {
            // get roles from the Admin classes
            foreach ($this->pool->getAdminServiceIds() as $id) {
                try {
                    $admin = $this->pool->getInstance($id);
                } catch (\Exception $e) {
                    continue;
                }

                $securityHandler = $admin->getSecurityHandler();

                foreach ($securityHandler->buildSecurityInformation($admin) as $role => $acls) {
                    $roles[$role] = $role;
                }
            }

            // get roles from the service container
            foreach ($this->pool->getContainer()->getParameter('security.role_hierarchy.roles') as $name => $rolesHierarchy) {
                $roles[$name] = $name . ': ' . implode(', ', $rolesHierarchy);
                
                foreach ($rolesHierarchy as $role) {
                    if (!isset($roles[$role])) {
                        $roles[$role] = $role;
                    }
                }
                
            }

            $options['choices'] = $roles;
        }

        return $options;
    }
}