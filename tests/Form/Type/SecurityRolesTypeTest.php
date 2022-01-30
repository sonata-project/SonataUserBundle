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

use PHPUnit\Framework\MockObject\MockObject;
use Sonata\UserBundle\Form\Type\SecurityRolesType;
use Sonata\UserBundle\Security\EditableRolesBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Quentin Fahrner <quentfahrner@gmail.com>
 */
final class SecurityRolesTypeTest extends TypeTestCase
{
    /**
     * @var MockObject&EditableRolesBuilderInterface
     */
    private MockObject $roleBuilder;

    protected function setUp(): void
    {
        $this->roleBuilder = $this->createMock(EditableRolesBuilderInterface::class);

        $this->roleBuilder->method('getRoles')->willReturn([
          'ROLE_FOO' => 'ROLE_FOO',
          'ROLE_USER' => 'ROLE_USER',
          'ROLE_ADMIN' => 'ROLE_ADMIN: ROLE_USER',
        ]);

        $this->roleBuilder->method('getRolesReadOnly')->willReturn([]);

        parent::setUp();
    }

    public function testGetDefaultOptions(): void
    {
        $type = new SecurityRolesType($this->roleBuilder);

        $optionResolver = new OptionsResolver();
        $type->configureOptions($optionResolver);

        $options = $optionResolver->resolve();
        static::assertCount(3, $options['choices']);
    }

    public function testGetParent(): void
    {
        $type = new SecurityRolesType($this->roleBuilder);

        static::assertSame(ChoiceType::class, $type->getParent());
    }

    public function testSubmitValidData(): void
    {
        $form = $this->factory->create(SecurityRolesType::class, null, [
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ]);

        $form->submit([0 => 'ROLE_FOO']);

        static::assertTrue($form->isValid());
        static::assertCount(1, $form->getData());
        static::assertContains('ROLE_FOO', $form->getData());
    }

    public function testSubmitInvalidData(): void
    {
        $form = $this->factory->create(SecurityRolesType::class, null, [
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ]);

        $form->submit([0 => 'ROLE_NOT_EXISTS']);

        static::assertFalse($form->isValid());
        static::assertSame([], $form->getData());
    }

    public function testSubmitWithHiddenRoleData(): void
    {
        $originalRoles = ['ROLE_SUPER_ADMIN', 'ROLE_USER'];

        $form = $this->factory->create(SecurityRolesType::class, $originalRoles, [
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ]);

        // we keep hidden ROLE_SUPER_ADMIN and delete available ROLE_USER
        $form->submit([0 => 'ROLE_USER']);

        static::assertNull($form->getTransformationFailure());
        static::assertTrue($form->isValid());
        static::assertCount(2, $form->getData());
        static::assertContains('ROLE_SUPER_ADMIN', $form->getData());
    }

    /**
     * @return FormExtensionInterface[]
     */
    protected function getExtensions(): array
    {
        $childType = new SecurityRolesType($this->roleBuilder);

        return [new PreloadedExtension([
          $childType,
        ], [])];
    }
}
