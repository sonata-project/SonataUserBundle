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

namespace Sonata\UserBundle\Tests\Security\RolesBuilder;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\SonataConfiguration;
use Sonata\UserBundle\Security\RolesBuilder\SecurityRolesBuilder;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class SecurityRolesBuilderTest extends TestCase
{
    /**
     * @var MockObject&AuthorizationCheckerInterface
     */
    private MockObject $authorizationChecker;

    private SonataConfiguration $configuration;

    /**
     * @var MockObject&TranslatorInterface
     */
    private MockObject $translator;

    /**
     * @var array<string, string[]>
     */
    private array $rolesHierarchy = ['ROLE_FOO' => ['ROLE_BAR', 'ROLE_ADMIN']];

    protected function setUp(): void
    {
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->configuration = new SonataConfiguration('title', 'logo', [
            'confirm_exit' => true,
            'default_admin_route' => 'show',
            'default_group' => 'group',
            'default_icon' => 'icon',
            'default_translation_domain' => 'label_catalogue',
            'dropdown_number_groups_per_colums' => 1,
            'form_type' => 'horizontal',
            'html5_validate' => true,
            'javascripts' => [],
            'js_debug' => true,
            'list_action_button_content' => 'text',
            'lock_protection' => true,
            'logo_content' => 'text',
            'mosaic_background' => 'background',
            'pager_links' => 1,
            'role_admin' => 'ROLE_SONATA_ADMIN',
            'role_super_admin' => 'ROLE_SUPER_ADMIN',
            'search' => true,
            'skin' => 'skin-blue',
            'sort_admins' => true,
            'stylesheets' => [],
            'use_bootlint' => true,
            'use_icheck' => true,
            'use_select2' => true,
            'use_stickyforms' => true,
        ]);
        $this->translator = $this->createMock(TranslatorInterface::class);
    }

    public function testGetRoles(): void
    {
        $securityRolesBuilder = new SecurityRolesBuilder(
            $this->authorizationChecker,
            $this->configuration,
            $this->translator,
            $this->rolesHierarchy
        );

        $this->authorizationChecker->method('isGranted')
            ->willReturn(true);

        $expected = [
            'ROLE_SUPER_ADMIN' => [
                'role' => 'ROLE_SUPER_ADMIN',
                'role_translated' => 'ROLE_SUPER_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_SONATA_ADMIN' => [
                'role' => 'ROLE_SONATA_ADMIN',
                'role_translated' => 'ROLE_SONATA_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_FOO' => [
                'role' => 'ROLE_FOO',
                'role_translated' => 'ROLE_FOO: ROLE_BAR, ROLE_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_BAR' => [
                'role' => 'ROLE_BAR',
                'role_translated' => 'ROLE_BAR',
                'is_granted' => true,
            ],
            'ROLE_ADMIN' => [
                'role' => 'ROLE_ADMIN',
                'role_translated' => 'ROLE_ADMIN',
                'is_granted' => true,
            ],
        ];

        static::assertSame($expected, $securityRolesBuilder->getExpandedRoles());
    }

    public function testGetRolesNotExpanded(): void
    {
        $securityRolesBuilder = new SecurityRolesBuilder(
            $this->authorizationChecker,
            $this->configuration,
            $this->translator,
            $this->rolesHierarchy
        );

        $this->authorizationChecker->method('isGranted')
            ->willReturn(true);

        $expected = [
            'ROLE_SUPER_ADMIN' => [
                'role' => 'ROLE_SUPER_ADMIN',
                'role_translated' => 'ROLE_SUPER_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_SONATA_ADMIN' => [
                'role' => 'ROLE_SONATA_ADMIN',
                'role_translated' => 'ROLE_SONATA_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_FOO' => [
                'role' => 'ROLE_FOO',
                'role_translated' => 'ROLE_FOO',
                'is_granted' => true,
            ],
            'ROLE_BAR' => [
                'role' => 'ROLE_BAR',
                'role_translated' => 'ROLE_BAR',
                'is_granted' => true,
            ],
            'ROLE_ADMIN' => [
                'role' => 'ROLE_ADMIN',
                'role_translated' => 'ROLE_ADMIN',
                'is_granted' => true,
            ],
        ];

        static::assertSame($expected, $securityRolesBuilder->getRoles(null));
    }

    public function testGetRolesWithExistingRole(): void
    {
        $this->rolesHierarchy['ROLE_STAFF'] = ['ROLE_SUPER_ADMIN', 'ROLE_SUPER_ADMIN'];

        $securityRolesBuilder = new SecurityRolesBuilder(
            $this->authorizationChecker,
            $this->configuration,
            $this->translator,
            $this->rolesHierarchy
        );

        $this->authorizationChecker->method('isGranted')
            ->willReturn(true);

        $expected = [
            'ROLE_SUPER_ADMIN' => [
                'role' => 'ROLE_SUPER_ADMIN',
                'role_translated' => 'ROLE_SUPER_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_SONATA_ADMIN' => [
                'role' => 'ROLE_SONATA_ADMIN',
                'role_translated' => 'ROLE_SONATA_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_FOO' => [
                'role' => 'ROLE_FOO',
                'role_translated' => 'ROLE_FOO: ROLE_BAR, ROLE_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_BAR' => [
                'role' => 'ROLE_BAR',
                'role_translated' => 'ROLE_BAR',
                'is_granted' => true,
            ],
            'ROLE_ADMIN' => [
                'role' => 'ROLE_ADMIN',
                'role_translated' => 'ROLE_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_STAFF' => [
                'role' => 'ROLE_STAFF',
                'role_translated' => 'ROLE_STAFF: ROLE_SUPER_ADMIN, ROLE_SUPER_ADMIN',
                'is_granted' => true,
            ],
        ];

        static::assertSame($expected, $securityRolesBuilder->getExpandedRoles());
    }
}
