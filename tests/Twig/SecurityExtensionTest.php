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
use Sonata\UserBundle\Security\RolesMatrixBuilder;
use Sonata\UserBundle\Twig\SecurityExtension;
use Symfony\Component\Form\FormView;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class SecurityExtensionTest extends TestCase
{
    private $rolesBuilder;
    private $environment;
    private $formView;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->rolesBuilder = $this->createMock(RolesMatrixBuilder::class);
        $this->environment = $this->createMock(\Twig_Environment::class);
        $this->formView = $this->createMock(FormView::class);
    }

    public function testGetName(): void
    {
        $securityExtension = new SecurityExtension($this->rolesBuilder);
        $this->assertSame('sonata_user_security_extension', $securityExtension->getName());
    }

    /**
     * @test
     */
    public function renderCustomRolesList(): void
    {
        $roles = ['ROLE_PARENT' => ['read_only' => false], 'ROLE_CHILD_1' => ['read_only' => false], 'ROLE_CHILD_2' => ['read_only' => false]];
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
            ->with('@SonataUser/Form/roles_list.html.twig', [
                'roles' => [
                    'ROLE_PARENT' => ['read_only' => false, 'form' => $form],
                    'ROLE_CHILD_1' => ['read_only' => false],
                    'ROLE_CHILD_2' => ['read_only' => false],
                ],
            ]);

        $securityExtension = new SecurityExtension($this->rolesBuilder);
        $securityExtension->renderCustomRolesList($this->environment, $this->formView);
    }

    /**
     * @test
     */
    public function renderTable(): void
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
            ->with('@SonataUser/Form/roles_row.html.twig', [
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

        $securityExtension = new SecurityExtension($this->rolesBuilder);
        $securityExtension->renderTable($this->environment, $this->formView);
    }
}
