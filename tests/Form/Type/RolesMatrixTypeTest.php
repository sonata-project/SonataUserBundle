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

use Sonata\UserBundle\Form\Type\RolesMatrixType;
use Sonata\UserBundle\Security\RolesBuilder\ExpandableRolesBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class RolesMatrixTypeTest extends TypeTestCase
{
    private $roleBuilder;

    public function testGetDefaultOptions(): void
    {
        $type = new RolesMatrixType($this->roleBuilder);

        $optionResolver = new OptionsResolver();
        $type->configureOptions($optionResolver);

        $options = $optionResolver->resolve();
        $this->assertCount(3, $options['choices']);
    }

    public function testGetParent(): void
    {
        $type = new RolesMatrixType($this->roleBuilder);

        $this->assertSame(ChoiceType::class, $type->getParent());
    }

    public function testSubmitValidData(): void
    {
        $form = $this->factory->create(RolesMatrixType::class, null, [
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
        $form = $this->factory->create(RolesMatrixType::class, null, [
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ]);

        $form->submit([0 => 'ROLE_NOT_EXISTS']);

        $this->assertFalse($form->isSynchronized());
        $this->assertNull($form->getData());
    }

    protected function getExtensions(): array
    {
        $this->roleBuilder = $this->createMock(ExpandableRolesBuilderInterface::class);

        $this->roleBuilder->method('getRoles')->willReturn([
          'ROLE_FOO' => 'ROLE_FOO',
          'ROLE_USER' => 'ROLE_USER',
          'ROLE_ADMIN' => 'ROLE_ADMIN: ROLE_USER',
        ]);

        $childType = new RolesMatrixType($this->roleBuilder);

        return [new PreloadedExtension([
          $childType,
        ], [])];
    }
}
