<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Admin\Entity;

use Sonata\AdminBundle\Admin\Admin;

class GroupAdmin extends Admin
{
    protected $list = array(
        'name' => array('identifier' => true),
        'roles'
    );

    protected $form = array(
        'name',
//        'roles'
    );

    public function getNewInstance()
    {
        $class = $this->getClass();

        return new $class('', array());
    }
}