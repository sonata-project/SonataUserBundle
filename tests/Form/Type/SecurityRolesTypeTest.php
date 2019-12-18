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

namespace Sonata\UserBundle\Tests\Form\Type;

use Sonata\UserBundle\Form\Type\SecurityRolesType;
use Sonata\UserBundle\Security\EditableRolesBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Quentin Fahrner <quentfahrner@gmail.com>
 */
class SecurityRolesTypeTest extends TypeTestCase
{
    protected $roleBuilder;

    public function testGetDefaultOptions(): void
    {
        $type = new SecurityRolesType($this->roleBuilder);

        $optionResolver = new OptionsResolver();
        $type->configureOptions($optionResolver);

        $options = $optionResolver->resolve();
        $this->assertCount(3, $options['choices']);
    }

    public function testGetParent(): void
    {
        $type = new SecurityRolesType($this->roleBuilder);

        $this->assertSame(ChoiceType::class, $type->getParent());
    }

    public function testSubmitValidData(): void
    {
        $form = $this->factory->create($this->getSecurityRolesTypeName(), null, [
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ]);

        $form->submit([0 => 'ROLE_FOO']);

        $this->assertTrue($form->isSynchronized());
        $this->assertCount(1, $form->getData());
        $this->assertContains('ROLE_FOO', $form->getData());
    }

    public function testSubmitInvalidData(): void
    {
        $form = $this->factory->create($this->getSecurityRolesTypeName(), null, [
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ]);

        $form->submit([0 => 'ROLE_NOT_EXISTS']);

        $this->assertFalse($form->isSynchronized());
        $this->assertNull($form->getData());
    }

    public function testSubmitWithHiddenRoleData(): void
    {
        $originalRoles = ['ROLE_SUPER_ADMIN', 'ROLE_USER'];

        $form = $this->factory->create($this->getSecurityRolesTypeName(), $originalRoles, [
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ]);

        // we keep hidden ROLE_SUPER_ADMIN and delete available ROLE_USER
        $form->submit([0 => 'ROLE_USER']);

        $this->assertNull($form->getTransformationFailure());
        $this->assertTrue($form->isSynchronized());
        $this->assertCount(2, $form->getData());
        $this->assertContains('ROLE_SUPER_ADMIN', $form->getData());
    }

    protected function getExtensions(): array
    {
        $this->roleBuilder = $this->createMock(EditableRolesBuilder::class);

        $this->roleBuilder->method('getRoles')->willReturn([
          'ROLE_FOO' => 'ROLE_FOO',
          'ROLE_USER' => 'ROLE_USER',
          'ROLE_ADMIN' => 'ROLE_ADMIN: ROLE_USER',
        ]);

        $this->roleBuilder->method('getRolesReadOnly')->willReturn([]);

        $childType = new SecurityRolesType($this->roleBuilder);

        return [new PreloadedExtension([
          $childType,
        ], [])];
    }

    private function getSecurityRolesTypeName(): string
    {
        return SecurityRolesType::class;
    }
}
