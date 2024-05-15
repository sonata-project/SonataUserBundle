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

namespace Sonata\UserBundle\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * @internal
 */
final class DoctrineMappingListener
{
    private bool $isArrayTypeAvailable;

    public function __construct(private string $userClass)
    {
        $this->isArrayTypeAvailable = class_exists('Doctrine\DBAL\Types\ArrayType');
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $metadata = $event->getClassMetadata();

        if (!$this->isArrayTypeAvailable) {
            return;
        }

        if ($metadata->getName() !== $this->userClass) {
            return;
        }

        if (!$metadata->hasField('roles')) {
            return;
        }

        /**
         * @psalm-suppress InvalidPropertyAssignmentValue
         */
        $metadata->fieldMappings['roles']['type'] = 'array';
    }
}
