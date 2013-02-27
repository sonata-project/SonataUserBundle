<?php

namespace Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileUpdatePasswordType extends AbstractType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', 'repeated', array(
                      'type' => 'password',
                      'options' => array('translation_domain' => 'FOSUserBundle'),
                      'first_options' => array('label' => 'form.password_new'),
                      'second_options' => array('label' => 'form.password_confirmation'),
                      'invalid_message' => 'fos_user.password.mismatch',
                      ))
                ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle', 'attr'=>array("readonly"=>"readonly")))
                ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'attr'=>array("readonly"=>"readonly")))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_user_update_password';
    }
}
