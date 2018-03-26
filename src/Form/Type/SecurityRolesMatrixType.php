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

namespace Sonata\UserBundle\Form\Type;

use Sonata\UserBundle\Security\RolesMatrixBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecurityRolesMatrixType extends AbstractType
{
    /**
     * @var RolesMatrixBuilder
     */
    protected $rolesBuilder;

    /**
     * @param RolesMatrixBuilder $rolesBuilder
     */
    public function __construct(RolesMatrixBuilder $rolesBuilder)
    {
        $this->rolesBuilder = $rolesBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $attr = $view->vars['attr'];

        if (isset($attr['class']) && empty($attr['class'])) {
            $attr['class'] = 'sonata-medium';
        }

        $view->vars['choice_translation_domain'] = false; // RolesBuilder all ready does translate them

        $view->vars['attr'] = $attr;
        $view->vars['label_permission'] = $this->rolesBuilder->getLabelPermission();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // make expanded default value
            'expanded' => true,

            'choices' => function (Options $options, $parentChoices) {
                if (!empty($parentChoices)) {
                    return [];
                }

                return $this->rolesBuilder->getAllRoles($options['choice_translation_domain'], $options['expanded']);
            },

            'choice_translation_domain' => function (Options $options, $value) {
                // if choice_translation_domain is true, then it's the same as translation_domain
                if (true === $value) {
                    $value = $options['translation_domain'];
                }
                if (null === $value) {
                    // no translation domain yet, try to ask sonata admin
                    $admin = null;
                    if (isset($options['sonata_admin'])) {
                        $admin = $options['sonata_admin'];
                    }
                    if (null === $admin && isset($options['sonata_field_description'])) {
                        $admin = $options['sonata_field_description']->getAdmin();
                    }
                    if (null !== $admin) {
                        $value = $admin->getTranslationDomain();
                    }
                }

                return $value;
            },

            'data_class' => null,
        ]);

        // Symfony 2.8 BC
        if (method_exists(FormTypeInterface::class, 'setDefaultOptions')) {
            $resolver->setDefault('choices_as_values', true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sonata_security_roles_matrix';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
