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

namespace Sonata\UserBundle\Security\RolesBuilder;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\SonataConfiguration;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 *
 * @phpstan-import-type Role from RolesBuilderInterface
 */
final class AdminRolesBuilder implements AdminRolesBuilderInterface
{
    /**
     * @var string[]
     */
    private array $excludeAdmins = [];

    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
        private Pool $pool,
        private SonataConfiguration $configuration,
        private TranslatorInterface $translator
    ) {
    }

    public function getPermissionLabels(): array
    {
        $permissionLabels = [];
        foreach ($this->getRoles() as $attributes) {
            if (isset($attributes['label'])) {
                $permissionLabels[$attributes['label']] = $attributes['label'];
            }
        }

        return $permissionLabels;
    }

    /**
     * @return string[]
     */
    public function getExcludeAdmins(): array
    {
        return $this->excludeAdmins;
    }

    public function addExcludeAdmin(string $exclude): void
    {
        $this->excludeAdmins[] = $exclude;
    }

    public function getRoles(?string $domain = null): array
    {
        $adminServiceCodes = array_diff($this->pool->getAdminServiceCodes(), $this->excludeAdmins);

        // get groups and admins sort by config
        $adminRoles = [];
        foreach ($this->pool->getAdminGroups() as $groupCode => $group) {
            foreach ($group['items'] as $item) {
                if (!isset($item['admin'])) {
                    continue;
                }

                $key = array_search($item['admin'], $adminServiceCodes, true);
                if (false === $key) {
                    continue;
                }
                unset($adminServiceCodes[$key]);

                $groupLabelTranslated = $this->translator->trans($group['label'], [], $group['translation_domain']);

                $adminRoles = array_merge($adminRoles, $this->getAdminRolesByAdminCode($item['admin'], $domain, $groupLabelTranslated, $groupCode));
            }
        }

        // admin with config "show_in_dashboard" set "false" or group not set, does not have group
        foreach ($adminServiceCodes as $code) {
            $adminRoles = array_merge($adminRoles, $this->getAdminRolesByAdminCode($code, $domain));
        }

        return $adminRoles;
    }

    /**
     * @return array<string, array<string, string|bool>>
     *
     * @phpstan-return array<string, Role>
     */
    private function getAdminRolesByAdminCode(string $code, ?string $domain = null, string $groupLabelTranslated = '', string $groupCode = ''): array
    {
        $adminRoles = [];
        $admin = $this->pool->getInstance($code);
        $securityHandler = $admin->getSecurityHandler();
        $baseRole = $securityHandler->getBaseRole($admin);
        $adminLabelTranslated = $admin->getTranslator()->trans($admin->getLabel() ?? '', [], $admin->getTranslationDomain());
        $isMasterAdmin = $this->isMaster($admin);
        foreach (array_keys($admin->getSecurityInformation()) as $key) {
            $role = sprintf($baseRole, $key);
            $adminRoles[$role] = [
                'role' => $role,
                'label' => $key,
                'role_translated' => $this->translateRole($role, $domain),
                'is_granted' => $isMasterAdmin || $this->authorizationChecker->isGranted($role),
                'admin_label' => $adminLabelTranslated,
                'admin_code' => $code,
                'group_label' => $groupLabelTranslated,
                'group_code' => $groupCode,
            ];
        }

        return $adminRoles;
    }

    /**
     * @param AdminInterface<object> $admin
     */
    private function isMaster(AdminInterface $admin): bool
    {
        return $admin->isGranted('MASTER') || $admin->isGranted('OPERATOR')
            || $this->authorizationChecker->isGranted($this->configuration->getOption('role_super_admin'));
    }

    private function translateRole(string $role, ?string $domain): string
    {
        if (null !== $domain) {
            return $this->translator->trans($role, [], $domain);
        }

        return $role;
    }
}
