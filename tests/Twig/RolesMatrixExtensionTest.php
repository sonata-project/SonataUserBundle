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

namespace Sonata\UserBundle\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Security\RolesBuilder\MatrixRolesBuilderInterface;
use Sonata\UserBundle\Twig\RolesMatrixExtension;
use Symfony\Component\Form\FormView;
use Twig\Environment;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class RolesMatrixExtensionTest extends TestCase
{
    private $rolesBuilder;
    private $environment;
    private $formView;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->rolesBuilder = $this->createMock(MatrixRolesBuilderInterface::class);
        $this->environment = $this->createMock(Environment::class);
        $this->formView = $this->createMock(FormView::class);
    }

    public function testRenderRolesListWithAdminLabel(): void
    {
        $roles = [
            'SUPER_TEST_ROLE' => [
                'role' => 'SUPER_TEST_ROLE',
                'role_translated' => 'SUPER TEST ROLE TRANSLATED',
                'is_granted' => true,
                'admin_label' => 'admin_name',
            ],
        ];
        $this->rolesBuilder
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn($roles);

        $this->formView
            ->expects($this->never())
            ->method('getIterator');

        $this->environment
            ->expects($this->once())
            ->method('render')
            ->with('@SonataUser/Form/roles_matrix_list.html.twig', ['roles' => []])
            ->willReturn('')
        ;

        $rolesMatrixExtension = new RolesMatrixExtension($this->rolesBuilder);
        $rolesMatrixExtension->renderRolesList($this->environment, $this->formView);
    }

    public function testRenderRolesList(): void
    {
        $roles = [
            'SUPER_TEST_ROLE' => [
                'role' => 'SUPER_TEST_ROLE',
                'role_translated' => 'SUPER TEST ROLE TRANSLATED',
                'is_granted' => true,
            ],
        ];
        $this->rolesBuilder
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn($roles);

        $form = new FormView();
        $form->vars['value'] = 'SUPER_TEST_ROLE';

        $this->formView
            ->method('getIterator')
            ->willReturn([$form]);

        $this->environment
            ->expects($this->once())
            ->method('render')
            ->with('@SonataUser/Form/roles_matrix_list.html.twig', [
                'roles' => [
                    'SUPER_TEST_ROLE' => [
                        'role' => 'SUPER_TEST_ROLE',
                        'role_translated' => 'SUPER TEST ROLE TRANSLATED',
                        'is_granted' => true,
                        'form' => $form,
                    ],
                ],
            ])
            ->willReturn('');

        $rolesMatrixExtension = new RolesMatrixExtension($this->rolesBuilder);
        $rolesMatrixExtension->renderRolesList($this->environment, $this->formView);
    }

    public function testRenderRolesListWithoutFormValue(): void
    {
        $roles = [
            'SUPER_TEST_ROLE' => [
                'role' => 'SUPER_TEST_ROLE',
                'role_translated' => 'SUPER TEST ROLE TRANSLATED',
                'is_granted' => true,
            ],
        ];
        $this->rolesBuilder
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn($roles);

        $form = new FormView();
        $form->vars['value'] = 'WRONG_VALUE';

        $this->formView
            ->method('getIterator')
            ->willReturn([$form]);

        $this->environment
            ->expects($this->once())
            ->method('render')
            ->with('@SonataUser/Form/roles_matrix_list.html.twig', [
                'roles' => [
                    'SUPER_TEST_ROLE' => [
                        'role' => 'SUPER_TEST_ROLE',
                        'role_translated' => 'SUPER TEST ROLE TRANSLATED',
                        'is_granted' => true,
                    ],
                ],
            ])
            ->willReturn('');

        $rolesMatrixExtension = new RolesMatrixExtension($this->rolesBuilder);
        $rolesMatrixExtension->renderRolesList($this->environment, $this->formView);
    }

    public function testRenderMatrixWithoutAdminLabels(): void
    {
        $roles = [
            'BASE_ROLE_FOO_%s' => [
                'role' => 'BASE_ROLE_FOO_EDIT',
                'label' => 'EDIT',
                'role_translated' => 'ROLE FOO TRANSLATED',
                'is_granted' => true,
            ],
        ];
        $this->rolesBuilder
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn($roles);

        $this->rolesBuilder
            ->expects($this->once())
            ->method('getPermissionLabels')
            ->willReturn(['EDIT', 'CREATE']);

        $this->formView
            ->expects($this->never())
            ->method('getIterator');

        $this->environment
            ->expects($this->once())
            ->method('render')
            ->with('@SonataUser/Form/roles_matrix.html.twig', [
                'grouped_roles' => [],
                'permission_labels' => ['EDIT', 'CREATE'],
            ])
            ->willReturn('');

        $rolesMatrixExtension = new RolesMatrixExtension($this->rolesBuilder);
        $rolesMatrixExtension->renderMatrix($this->environment, $this->formView);
    }

    public function testRenderMatrix(): void
    {
        $roles = [
            'BASE_ROLE_FOO_EDIT' => [
                'role' => 'BASE_ROLE_FOO_EDIT',
                'label' => 'EDIT',
                'role_translated' => 'ROLE FOO TRANSLATED',
                'admin_label' => 'fooadmin',
                'is_granted' => true,
            ],
        ];
        $this->rolesBuilder
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn($roles);

        $this->rolesBuilder
            ->expects($this->once())
            ->method('getPermissionLabels')
            ->willReturn(['EDIT', 'CREATE']);

        $form = new FormView();
        $form->vars['value'] = 'BASE_ROLE_FOO_EDIT';

        $this->formView
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn([$form]);

        $this->environment
            ->expects($this->once())
            ->method('render')
            ->with('@SonataUser/Form/roles_matrix.html.twig', [
                'grouped_roles' => [
                    'fooadmin' => [
                        'BASE_ROLE_FOO_EDIT' => [
                            'role' => 'BASE_ROLE_FOO_EDIT',
                            'label' => 'EDIT',
                            'role_translated' => 'ROLE FOO TRANSLATED',
                            'admin_label' => 'fooadmin',
                            'is_granted' => true,
                            'form' => $form,
                        ],
                    ],
                ],
                'permission_labels' => ['EDIT', 'CREATE'],
            ])
            ->willReturn('');

        $rolesMatrixExtension = new RolesMatrixExtension($this->rolesBuilder);
        $rolesMatrixExtension->renderMatrix($this->environment, $this->formView);
    }

    public function testRenderMatrixFormVarsNotSet(): void
    {
        $roles = [
            'BASE_ROLE_FOO_%s' => [
                'role' => 'BASE_ROLE_FOO_EDIT',
                'label' => 'EDIT',
                'role_translated' => 'ROLE FOO TRANSLATED',
                'admin_label' => 'fooadmin',
                'is_granted' => true,
            ],
        ];
        $this->rolesBuilder
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn($roles);

        $this->rolesBuilder
            ->expects($this->once())
            ->method('getPermissionLabels')
            ->willReturn(['EDIT', 'CREATE']);

        $form = new FormView();
        $form->vars['value'] = 'WRONG_VALUE';

        $this->formView
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn([$form]);

        $this->environment
            ->expects($this->once())
            ->method('render')
            ->with('@SonataUser/Form/roles_matrix.html.twig', [
                'grouped_roles' => [
                    'fooadmin' => [
                        'BASE_ROLE_FOO_%s' => [
                            'role' => 'BASE_ROLE_FOO_EDIT',
                            'label' => 'EDIT',
                            'role_translated' => 'ROLE FOO TRANSLATED',
                            'admin_label' => 'fooadmin',
                            'is_granted' => true,
                        ],
                    ],
                ],
                'permission_labels' => ['EDIT', 'CREATE'],
            ])
            ->willReturn('');

        $rolesMatrixExtension = new RolesMatrixExtension($this->rolesBuilder);
        $rolesMatrixExtension->renderMatrix($this->environment, $this->formView);
    }
}
