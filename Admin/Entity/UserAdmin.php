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
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class UserAdmin extends Admin
{

    protected $list = array(
        'username' => array('identifier' => true),
        'email',
        'enabled',
        'locked',
        'createdAt',
    );

    protected $form = array(
        'username',
        'email',
        'enabled',
        'locked',
        'expired',
        'credentialsExpired',
        'credentialsExpireAt',
        'groups'
    );

    protected $formGroups = array(
        'General' => array(
            'fields' => array('username', 'email', 'plainPassword')
        ),
        'Groups' => array(
            'fields' => array('groups')
        ),
        'Management' => array(
            'fields' => array('roles', 'locked', 'expired', 'enabled', 'credentialsExpired', 'credentialsExpireAt')
        )
    );

    protected $formOptions = array(
        'validation_groups' => 'admin'
    );

    protected $filter = array(
        'username',
        'locked',
        'email',
        'id',
    );

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->addType('roles', 'sonata_security_roles', array(
            'multiple' => true,
//            'expanded' => true,
        ), array(
            'type' => 'choice'
        ));
    }
}