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

namespace Sonata\UserBundle\Twig;

use Sonata\UserBundle\Security\EditableRolesBuilder;
use Symfony\Component\Form\FormView;

/**
 * @author Christian Gripp <mail@core23.de>
 * @author Cengizhan Çalışkan <cengizhancaliskan@gmail.com>
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class SecurityExtension extends \Twig_Extension
{
    /**
     * @var EditableRolesBuilder
     */
    private $rolesBuilder;

    /**
     * @param EditableRolesBuilder $rolesBuilder
     */
    public function __construct(EditableRolesBuilder $rolesBuilder)
    {
        $this->rolesBuilder = $rolesBuilder;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('renderTable', [$this, 'renderTable'], ['needs_environment' => true]),
            new \Twig_SimpleFunction('renderCustomRolesList', [$this, 'renderCustomRolesList'], ['needs_environment' => true]),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sonata_user_security_extension';
    }

    /**
     * @param \Twig_Environment $environment
     * @param FormView          $form
     *
     * @return string
     */
    public function renderCustomRolesList(\Twig_Environment $environment, FormView $form)
    {
        $roles = $this->rolesBuilder->getCustomRolesForView();
        foreach ($roles as $mainRole => $attributes) {
            foreach ($form->getIterator() as $child) {
                if ($child->vars['value'] == $mainRole) {
                    $roles[$mainRole] = [
                        'read_only' => $attributes['read_only'] ?? false,
                        'form' => $child,
                    ];
                }
            }
        }

        return $environment->render('@SonataUser/Form/roles_list.html.twig', [
            'roles' => $roles,
        ]);
    }

    /**
     * @param \Twig_Environment $environment
     * @param FormView          $form
     *
     * @return string
     */
    public function renderTable(\Twig_Environment $environment, FormView $form)
    {
        $roles = $this->rolesBuilder->getAdminRolesForView();
        foreach ($roles as $baseRole => $attributes) {
            foreach ($attributes['permissions'] as $permission => $readOnly) {
                foreach ($form->getIterator() as $child) {
                    if ($child->vars['value'] == sprintf($baseRole, $permission)) {
                        $roles[$baseRole]['permissions'][$permission] = ['form' => $child, 'read_only' => $readOnly];
                    }
                }
            }
        }

        return $environment->render('@SonataUser/Form/roles_row.html.twig', [
            'roles' => $roles,
        ]);
    }
}
