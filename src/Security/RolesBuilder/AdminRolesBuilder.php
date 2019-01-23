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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class AdminRolesBuilder implements AdminRolesBuilderInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string []
     */
    private $excludeAdmins = [];

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        Pool $pool,
        TranslatorInterface $translator
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->pool = $pool;
        $this->translator = $translator;
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

    public function getExcludeAdmins(): array
    {
        return $this->excludeAdmins;
    }

    public function addExcludeAdmin(string $exclude): void
    {
        $this->excludeAdmins[] = $exclude;
    }

    public function getRoles(string $domain = null): array
    {
        $adminRoles = [];
        foreach ($this->pool->getAdminServiceIds() as $id) {
            if (\in_array($id, $this->excludeAdmins, true)) {
                continue;
            }

            $admin = $this->pool->getInstance($id);
            $securityHandler = $admin->getSecurityHandler();
            $baseRole = $securityHandler->getBaseRole($admin);
            foreach (array_keys($admin->getSecurityInformation()) as $key) {
                $role = sprintf($baseRole, $key);
                $adminRoles[$role] = [
                    'role' => $role,
                    'label' => $key,
                    'role_translated' => $this->translateRole($role, $domain),
                    'is_granted' => $this->isMaster($admin) || $this->authorizationChecker->isGranted($role),
                    'admin_label' => $admin->getTranslator()->trans($admin->getLabel()),
                ];
            }
        }

        return $adminRoles;
    }

    private function isMaster(AdminInterface $admin): bool
    {
        return $admin->isGranted('MASTER') || $admin->isGranted('OPERATOR')
            || $this->authorizationChecker->isGranted($this->pool->getOption('role_super_admin'));
    }

    private function translateRole(string $role, $domain): string
    {
        if ($domain) {
            return $this->translator->trans($role, [], $domain);
        }

        return $role;
    }
}
