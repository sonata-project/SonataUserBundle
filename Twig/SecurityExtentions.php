<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Sonata\UserBundle\Twig;


class SecurityExtentions extends \Twig_Extension {
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'roleInTable' => new \Twig_Function_Method($this, 'roleInTable'),
            'isReadOnly' => new \Twig_Function_Method($this, 'isReadOnly')
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sizannia_jquery_tools_extension';
    }

    /**
     * @param $value
     * @param array $_array
     * @return bool
     */
    public function isReadOnly($value, array $_array)
    {
        return in_array($value, $_array);
    }

    /**
     * @param $needle
     * @param $haystack
     * @return bool
     */
    public function roleInTable($needle, $haystack) {
        foreach ($haystack as $item) {
            if ($item->data == $needle) {
                return true;
            }
        }
        return false;
    }
}