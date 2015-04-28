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

namespace Sonata\UserBundle\Form\Type;

use Sonata\UserBundle\Form\Transformer\RestoreRolesTransformer;
use Sonata\UserBundle\Security\EditableRolesBuilder;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

class SecurityRolesType extends AbstractType
{
    protected $rolesBuilder;
    /**
     * @param EditableRolesBuilder $rolesBuilder
     */
    public function __construct(EditableRolesBuilder $rolesBuilder)
    {
        $this->rolesBuilder = $rolesBuilder;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['read_only_choices'] = $options['read_only_choices'];
        $view->vars['labelPermission'] = $this->rolesBuilder->getLabelPermission();
        $view->vars['labelAdmin'] = $this->rolesBuilder->getLabelAdmin();
    }
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        list($roles, $rolesReadOnly) = $this->rolesBuilder->getRoles();
        $resolver->setDefaults(array(
            'choices' => function (Options $options, $parentChoices) use ($roles)
            {
                return empty($parentChoices) ? $roles : array();
            },
            'read_only_choices' => $rolesReadOnly,
            'data_class' => null,
            'attr' => array(
                'class' => ''
            ),
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_security_roles';
    }
}
