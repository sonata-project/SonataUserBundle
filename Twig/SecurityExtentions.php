<?php
/**
 * Created by PhpStorm.
 * User: thibault.algrin
 * Date: 27/04/2015
 * Time: 16:37
 */

namespace Sonata\UserBundle\Twig;


class SecurityExtentions extends \Twig_Extension {
    public function getFunctions()
    {
        return array(
            'roleInTable' => new \Twig_Function_Method($this, 'roleInTable'),
            'isReadOnly' => new \Twig_Function_Method($this, 'isReadOnly')
        );
    }
    public function getName()
    {
        return 'sizannia_jquery_tools_extension';
    }
    public function isReadOnly($value, array $_array) {
        return in_array($value, $_array);
    }
    public function roleInTable($needle, $haystack) {
        foreach ($haystack as $item) {
            if ($item->data == $needle) {
                return true;
            }
        }
        return false;
    }
}