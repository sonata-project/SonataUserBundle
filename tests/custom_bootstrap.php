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

/*
 * We MUST remove file this when support for "friendsofsymfony/user-bundle" is dropped
 * or adapted to work with "doctrine/common:^3".
 */

if (!interface_exists(\Doctrine\Common\Persistence\ObjectManager::class)) {
    class_alias(\Doctrine\Persistence\ObjectManager::class, \Doctrine\Common\Persistence\ObjectManager::class);
}

if (!class_exists(\Doctrine\Common\Persistence\Event\LifecycleEventArgs::class)) {
    class_alias(\Doctrine\Persistence\Event\LifecycleEventArgs::class, \Doctrine\Common\Persistence\Event\LifecycleEventArgs::class);
}
