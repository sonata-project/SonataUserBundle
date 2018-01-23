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

/**
 * @author Christian Gripp <mail@core23.de>
 * @author Cengizhan Çalışkan <cengizhancaliskan@gmail.com>
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class SecurityExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('roleInTable', [$this, 'roleInTable']),
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
     * @param string $role
     * @param array  $roles
     *
     * @return bool
     */
    public function roleInTable($role, $roles)
    {
        foreach ($roles as $item) {
            if ($item->data === $role) {
                return true;
            }
        }

        return false;
    }
}
