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

namespace Sonata\UserBundle\Tests\Form\Transformer;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Form\Transformer\RestoreRolesTransformer;
use Sonata\UserBundle\Security\EditableRolesBuilder;

class RestoreRolesTransformerTest extends TestCase
{
    public function testInvalidStateTransform(): void
    {
        $this->expectException(\RuntimeException::class);

        $roleBuilder = $this->createMock(EditableRolesBuilder::class);

        $transformer = new RestoreRolesTransformer($roleBuilder);
        $transformer->transform([]);
    }

    public function testInvalidStateReverseTransform(): void
    {
        $this->expectException(\RuntimeException::class);

        $roleBuilder = $this->createMock(EditableRolesBuilder::class);

        $transformer = new RestoreRolesTransformer($roleBuilder);
        $transformer->reverseTransform([]);
    }

    public function testValidTransform(): void
    {
        $roleBuilder = $this->createMock(EditableRolesBuilder::class);

        $transformer = new RestoreRolesTransformer($roleBuilder);
        $transformer->setOriginalRoles([]);

        $data = ['ROLE_FOO'];

        $this->assertSame($data, $transformer->transform($data));
    }

    public function testValidReverseTransform(): void
    {
        $roleBuilder = $this->createMock(EditableRolesBuilder::class);

        $roleBuilder->expects($this->once())->method('getRoles')->willReturn([]);

        $transformer = new RestoreRolesTransformer($roleBuilder);
        $transformer->setOriginalRoles(['ROLE_HIDDEN']);

        $data = ['ROLE_FOO'];

        $this->assertSame(['ROLE_FOO', 'ROLE_HIDDEN'], $transformer->reverseTransform($data));
    }

    public function testTransformAllowEmptyOriginalRoles(): void
    {
        $roleBuilder = $this->createMock(EditableRolesBuilder::class);

        $transformer = new RestoreRolesTransformer($roleBuilder);
        $transformer->setOriginalRoles(null);

        $data = ['ROLE_FOO'];

        $this->assertSame($data, $transformer->transform($data));
    }

    public function testReverseTransformAllowEmptyOriginalRoles(): void
    {
        $roleBuilder = $this->createMock(EditableRolesBuilder::class);

        $roleBuilder->expects($this->once())->method('getRoles')->willReturn([]);

        $transformer = new RestoreRolesTransformer($roleBuilder);
        $transformer->setOriginalRoles(null);

        $data = ['ROLE_FOO'];

        $this->assertSame(['ROLE_FOO'], $transformer->reverseTransform($data));
    }

    public function testReverseTransformRevokedHierarchicalRole(): void
    {
        $roleBuilder = $this->createMock(EditableRolesBuilder::class);

        $availableRoles = [
            'ROLE_SONATA_ADMIN' => 'ROLE_SONATA_ADMIN',
            'ROLE_COMPANY_PERSONAL_MODERATOR' => 'ROLE_COMPANY_PERSONAL_MODERATOR: ROLE_COMPANY_USER',
            'ROLE_COMPANY_NEWS_MODERATOR' => 'ROLE_COMPANY_NEWS_MODERATOR: ROLE_COMPANY_USER',
            'ROLE_COMPANY_BOOKKEEPER' => 'ROLE_COMPANY_BOOKKEEPER: ROLE_COMPANY_USER',
            'ROLE_USER' => 'ROLE_USER',
        ];
        $roleBuilder->expects($this->once())->method('getRoles')->willReturn($availableRoles);

        // user roles
        $userRoles = ['ROLE_COMPANY_PERSONAL_MODERATOR', 'ROLE_COMPANY_NEWS_MODERATOR', 'ROLE_COMPANY_BOOKKEEPER'];
        $transformer = new RestoreRolesTransformer($roleBuilder);
        $transformer->setOriginalRoles($userRoles);

        // now we want to revoke role ROLE_COMPANY_PERSONAL_MODERATOR
        $revokedRole = array_shift($userRoles);
        $processedRoles = $transformer->reverseTransform($userRoles);

        $this->assertNotContains($revokedRole, $processedRoles);
    }

    public function testReverseTransformHiddenRole(): void
    {
        $roleBuilder = $this->createMock(EditableRolesBuilder::class);

        $availableRoles = [
            'ROLE_SONATA_ADMIN' => 'ROLE_SONATA_ADMIN',
            'ROLE_ADMIN' => 'ROLE_ADMIN: ROLE_USER ROLE_COMPANY_ADMIN',
        ];
        $roleBuilder->expects($this->once())->method('getRoles')->willReturn($availableRoles);

        // user roles
        $userRoles = ['ROLE_USER', 'ROLE_SUPER_ADMIN'];
        $transformer = new RestoreRolesTransformer($roleBuilder);
        $transformer->setOriginalRoles($userRoles);

        // add a new role
        $userRoles[] = 'ROLE_SONATA_ADMIN';
        // remove existing user role that is not availableRoles
        unset($userRoles[array_search('ROLE_SUPER_ADMIN', $userRoles, true)]);
        $processedRoles = $transformer->reverseTransform($userRoles);

        $this->assertContains('ROLE_SUPER_ADMIN', $processedRoles);
    }
}
