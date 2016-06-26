<?php

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
 */
final class SecurityExtensions extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'roleInTable' => new \Twig_Function_Method($this, 'roleInTable'),
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sonata_user_security_extension';
    }

    /**
     * @param mixed $needle
     * @param array $haystack
     *
     * @return bool
     */
    public function roleInTable($needle, $haystack)
    {
        foreach ($haystack as $item) {
            if ($item->data == $needle) {
                return true;
            }
        }

        return false;
    }
}
