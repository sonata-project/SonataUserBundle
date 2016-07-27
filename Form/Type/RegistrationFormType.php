<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var array
     */
    protected $mergeOptions;

    /**
     * @param string $class        The User class name
     * @param array  $mergeOptions Add options to elements
     */
    public function __construct($class, array $mergeOptions = [])
    {
        $this->class = $class;
        $this->mergeOptions = $mergeOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array_merge([
                'label'              => 'form.username',
                'translation_domain' => 'SonataUserBundle',
            ], $this->mergeOptions))
            ->add('email', 'email', array_merge([
                'label'              => 'form.email',
                'translation_domain' => 'SonataUserBundle',
            ], $this->mergeOptions))
            ->add('plainPassword', 'repeated', array_merge([
                'type'            => 'password',
                'options'         => ['translation_domain' => 'SonataUserBundle'],
                'first_options'   => array_merge([
                    'label' => 'form.password',
                ], $this->mergeOptions),
                'second_options'  => array_merge([
                    'label' => 'form.password_confirmation',
                ], $this->mergeOptions),
                'invalid_message' => 'fos_user.password.mismatch',
            ], $this->mergeOptions));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->class,
            'intention'  => 'registration',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sonata_user_registration';
    }
}
