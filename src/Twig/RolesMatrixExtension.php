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

use Sonata\UserBundle\Security\RolesBuilderInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Christian Gripp <mail@core23.de>
 * @author Cengizhan Çalışkan <cengizhancaliskan@gmail.com>
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class RolesMatrixExtension extends \Twig_Extension
{
    /**
     * @var RolesBuilderInterface
     */
    private $rolesBuilder;

    public function __construct(RolesBuilderInterface $rolesBuilder)
    {
        $this->rolesBuilder = $rolesBuilder;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction(
                'renderMatrix',
                [$this, 'renderMatrix'],
                ['needs_environment' => true]
            ),
            new \Twig_SimpleFunction(
                'renderCustomRolesList',
                [$this, 'renderCustomRolesList'],
                ['needs_environment' => true]
            ),
        ];
    }

    public function getName(): string
    {
        return self::class;
    }

    public function renderCustomRolesList(\Twig_Environment $environment, FormView $form): string
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

        return (string) $environment->render('@SonataUser/Form/roles_matrix_list.html.twig', [
            'roles' => $roles,
        ]);
    }

    public function renderMatrix(\Twig_Environment $environment, FormView $form): string
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

        return (string) $environment->render('@SonataUser/Form/roles_matrix_row.html.twig', [
            'roles' => $roles,
        ]);
    }
}
