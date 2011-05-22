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

class SecurityRolesType extends ChoiceType
{
    protected $rolesHierarchy = array();

    public function __construct($container)
    {
        $this->rolesHierarchy = $container->getParameter('security.role_hierarchy.roles');
    }

    public function getDefaultOptions(array $options)
    {
        $options = parent::getDefaultOptions($options);

        $roles = array();
        if (count($options['choices']) == 0) {
            foreach ($this->rolesHierarchy as $name => $rolesHierarchy) {
                $roles[$name] = $name;
                foreach ($rolesHierarchy as $role) {
                    if (!isset($roles[$role])) {
                        $roles[$role] = ' -- '.$role;
                    }
                }
            }

            $options['choices'] = $roles;
        }

        return $options;
    }
}