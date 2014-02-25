<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends AbstractType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                'label' => 'form.username',
                'translation_domain' => 'FOSUserBundle',
                'horizontal_input_wrapper_class' => "col-lg-8",
                'horizontal_label_class' => "col-lg-4 control-label"
            ))
            ->add('email', 'email', array(
                'label' => 'form.email',
                'translation_domain' => 'FOSUserBundle',
                'horizontal_input_wrapper_class' => "col-lg-8",
                'horizontal_label_class' => "col-lg-4 control-label"
            ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array(
                    'label' => 'form.password',
                    'horizontal_input_wrapper_class' => "col-lg-8",
                    'horizontal_label_class' => "col-lg-4 control-label"
                ),
                'second_options' => array(
                    'label' => 'form.password_confirmation',
                    'horizontal_input_wrapper_class' => "col-lg-8",
                    'horizontal_label_class' => "col-lg-4 control-label"
                ),
                'invalid_message' => 'fos_user.password.mismatch',
                'horizontal_input_wrapper_class' => "col-lg-8",
                'horizontal_label_class' => "col-lg-4 control-label"
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'registration',
        ));
    }

    public function getName()
    {
        return 'sonata_user_registration';
    }
}
