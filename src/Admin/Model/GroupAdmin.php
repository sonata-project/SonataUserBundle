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

namespace Sonata\UserBundle\Admin\Model;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\UserBundle\Form\Type\SecurityRolesType;

class GroupAdmin extends AbstractAdmin
{
    protected $formOptions = [
        'validation_groups' => 'Registration',
    ];

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name')
            ->add('roles');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('name');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->tab('Group')
                ->with('General', ['class' => 'col-md-6'])
                    ->add('name')
                ->end()
            ->end()
            ->tab('Security')
                ->with('Roles', ['class' => 'col-md-12'])
                    ->add('roles', SecurityRolesType::class, [
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                    ])
                ->end()
            ->end();
    }
}
