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
use Sonata\UserBundle\Security\RolesBuilderInterface;
use Sonata\UserBundle\Twig\RolesMatrixExtension;
use Symfony\Component\Form\FormView;

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
    public function setUp(): void
    {
        $this->rolesBuilder = $this->createMock(RolesBuilderInterface::class);
        $this->environment = $this->createMock(\Twig_Environment::class);
        $this->formView = $this->createMock(FormView::class);
    }

    public function testGetName(): void
    {
        $rolesMatrixExtension = new RolesMatrixExtension($this->rolesBuilder);
        $this->assertSame(RolesMatrixExtension::class, $rolesMatrixExtension->getName());
    }

    /**
     * @test
     */
    public function renderCustomRolesList(): void
    {
        $roles = [
            'ROLE_PARENT' => ['read_only' => false],
            'ROLE_CHILD_1' => ['read_only' => false],
            'ROLE_CHILD_2' => ['read_only' => false],
        ];
        $this->rolesBuilder
            ->expects($this->once())
            ->method('getCustomRolesForView')
            ->willReturn($roles);

        $form = new FormView();
        $form->vars['value'] = 'ROLE_PARENT';

        $this->formView
            ->method('getIterator')
            ->willReturn([$form]);

        $this->environment
            ->expects($this->once())
            ->method('render')
            ->with('@SonataUser/Form/roles_matrix_list.html.twig', [
                'roles' => [
                    'ROLE_PARENT' => ['read_only' => false, 'form' => $form],
                    'ROLE_CHILD_1' => ['read_only' => false],
                    'ROLE_CHILD_2' => ['read_only' => false],
                ],
            ]);

        $rolesMatrixExtension = new RolesMatrixExtension($this->rolesBuilder);
        $rolesMatrixExtension->renderCustomRolesList($this->environment, $this->formView);
    }

    /**
     * @test
     */
    public function renderMatrix(): void
    {
        $roles = [
            'ROLE_SONATA_ADMIN_FOO_%s' => [
                'label' => 'Foo Admin Translated',
                'permissions' => [
                    'EDITOR' => false,
                ],
            ],
        ];
        $this->rolesBuilder
            ->expects($this->once())
            ->method('getAdminRolesForView')
            ->willReturn($roles);

        $form = new FormView();
        $form->vars['value'] = 'ROLE_SONATA_ADMIN_FOO_EDITOR';

        $this->formView
            ->method('getIterator')
            ->willReturn([$form]);

        $this->environment
            ->expects($this->once())
            ->method('render')
            ->with('@SonataUser/Form/roles_matrix_row.html.twig', [
                'roles' => [
                    'ROLE_SONATA_ADMIN_FOO_%s' => [
                        'label' => 'Foo Admin Translated',
                        'permissions' => [
                            'EDITOR' => [
                                'form' => $form,
                                'read_only' => false,
                            ],
                        ],
                    ],
                ],
            ]);

        $rolesMatrixExtension = new RolesMatrixExtension($this->rolesBuilder);
        $rolesMatrixExtension->renderMatrix($this->environment, $this->formView);
    }
}
