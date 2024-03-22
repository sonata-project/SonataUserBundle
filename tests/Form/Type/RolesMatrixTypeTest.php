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
use Sonata\UserBundle\Form\Type\RolesMatrixType;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Security\RolesBuilder\ExpandableRolesBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class RolesMatrixTypeTest extends TypeTestCase
{
    /**
     * @var MockObject&ExpandableRolesBuilderInterface
     */
    private MockObject $roleBuilder;

    protected function setUp(): void
    {
        $this->roleBuilder = $this->createMock(ExpandableRolesBuilderInterface::class);

        $this->roleBuilder->method('getRoles')->willReturn([
            'ROLE_FOO' => 'ROLE_FOO',
            // Not returned by the RolesMatrix because it can't be changed
            UserInterface::ROLE_DEFAULT => UserInterface::ROLE_DEFAULT,
            'ROLE_ADMIN' => 'ROLE_ADMIN: ROLE_USER',
        ]);

        parent::setUp();
    }

    public function testGetDefaultOptions(): void
    {
        $type = new RolesMatrixType($this->roleBuilder);

        $optionResolver = new OptionsResolver();
        $type->configureOptions($optionResolver);

        $options = $optionResolver->resolve();
        $choices = $options['choices'];
        static::assertCount(2, $choices);
        static::assertNotContains(UserInterface::ROLE_DEFAULT, $choices);
        static::assertTrue($options['expanded']);
        static::assertTrue($options['multiple']);
    }

    public function testDifferentExcludedRoles(): void
    {
        $type = new RolesMatrixType($this->roleBuilder);

        $optionResolver = new OptionsResolver();
        $type->configureOptions($optionResolver);

        $options = $optionResolver->resolve([
            'excluded_roles' => ['ROLE_FOO'],
        ]);
        $choices = $options['choices'];
        static::assertCount(2, $choices);
        static::assertNotContains('ROLE_FOO', $choices);
        static::assertContains(UserInterface::ROLE_DEFAULT, $choices);
    }

    public function testNotExistExcludedRoles(): void
    {
        $type = new RolesMatrixType($this->roleBuilder);

        $optionResolver = new OptionsResolver();
        $type->configureOptions($optionResolver);

        $options = $optionResolver->resolve([
            'excluded_roles' => ['ROLE_BAR'],
        ]);
        $choices = $options['choices'];
        static::assertCount(3, $choices);
        static::assertContains('ROLE_FOO', $choices);
        static::assertNotContains('ROLE_BAR', $choices);
        static::assertContains(UserInterface::ROLE_DEFAULT, $choices);
    }

    public function testGetParent(): void
    {
        $type = new RolesMatrixType($this->roleBuilder);

        static::assertSame(ChoiceType::class, $type->getParent());
    }

    public function testSubmitValidData(): void
    {
        $form = $this->factory->create(RolesMatrixType::class, null, [
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
        $form = $this->factory->create(RolesMatrixType::class, null, [
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ]);

        $form->submit([0 => 'ROLE_NOT_EXISTS']);

        static::assertFalse($form->isValid());
        static::assertSame([], $form->getData());
    }

    public function testSubmitExcludedData(): void
    {
        $form = $this->factory->create(RolesMatrixType::class, null, [
            'multiple' => true,
            'expanded' => true,
            'required' => false,
            'excluded_roles' => ['ROLE_FOO'],
        ]);

        $form->submit([0 => 'ROLE_FOO']);

        static::assertFalse($form->isValid());
        static::assertSame([], $form->getData());
    }

    /**
     * @return FormExtensionInterface[]
     */
    protected function getExtensions(): array
    {
        $childType = new RolesMatrixType($this->roleBuilder);

        return [new PreloadedExtension([
            $childType,
        ], [])];
    }
}
