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
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Sonata\AdminBundle\Admin\Pool;

class SecurityRolesType extends ChoiceType
{
    protected $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->setAttribute('read_only_choices', $options['read_only_choices']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        parent::buildView($view, $form);

        $attr = $view->get('attr', array());

        if (isset($attr['class']) && empty($attr['class'])) {
            $attr['class'] = 'sonata-medium';
        }

        $view->set('attr', $attr);

        $view->set('read_only_choices', $form->getAttribute('read_only_choices'));
    }

    public function getDefaultOptions(array $options)
    {
        $options = parent::getDefaultOptions($options);

        $roles = array();
        $rolesReadOnly = array();
        if (count($options['choices']) == 0) {
            $securityContext = $this->pool->getContainer()->get('security.context');

            // get roles from the Admin classes
            foreach ($this->pool->getAdminServiceIds() as $id) {
                try {
                    $admin = $this->pool->getInstance($id);
                } catch (\Exception $e) {
                    continue;
                }

                $isMaster = $admin->isGranted('MASTER');
                $securityHandler = $admin->getSecurityHandler();
                // TODO get the base role from the admin or security handler
                $baseRole = $securityHandler->getBaseRole($admin);

                foreach ($admin->getSecurityInformation() as $role => $permissions) {
                    $role = sprintf($baseRole, $role);
                    if ($isMaster) {
                        // if the user has the MASTER permission, allow to grant access the admin roles to other users
                        $roles[$role] = $role;
                    } elseif ($securityContext->isGranted($role)) {
                        // although the user has no MASTER permission, allow the currently logged in user to view the role
                        $rolesReadOnly[$role] = $role;
                    }
                }
            }

            // get roles from the service container
            foreach ($this->pool->getContainer()->getParameter('security.role_hierarchy.roles') as $name => $rolesHierarchy) {

                if ($securityContext->isGranted($name)) {
                    $roles[$name] = $name . ': ' . implode(', ', $rolesHierarchy);

                    foreach ($rolesHierarchy as $role) {
                        if (!isset($roles[$role])) {
                            $roles[$role] = $role;
                        }
                    }
                }
            }
        }

        $options['choices'] = $roles;
        $options['read_only_choices'] = $rolesReadOnly;

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_security_roles';
    }
}