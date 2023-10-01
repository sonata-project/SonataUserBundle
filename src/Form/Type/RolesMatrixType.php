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

use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Security\RolesBuilder\ExpandableRolesBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 *
 * @psalm-suppress MissingTemplateParam https://github.com/phpstan/phpstan-symfony/issues/320
 */
final class RolesMatrixType extends AbstractType
{
    public function __construct(private ExpandableRolesBuilderInterface $rolesBuilder)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'expanded' => true,
            'multiple' => true,
            'choices' => function (Options $options, ?array $parentChoices): array {
                if (null !== $parentChoices && [] !== $parentChoices) {
                    return [];
                }

                $roles = $this->rolesBuilder->getRoles($options['choice_translation_domain']);
                $roles = array_keys($roles);
                $roles = array_diff($roles, $options['excluded_roles']);

                return array_combine($roles, $roles);
            },
            'choice_translation_domain' => static function (Options $options, bool|string|null $value): bool|string|null {
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
            'excluded_roles' => [UserInterface::ROLE_DEFAULT],
            'data_class' => null,
        ]);

        $resolver->addAllowedTypes('excluded_roles', 'string[]');
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sonata_roles_matrix';
    }
}
