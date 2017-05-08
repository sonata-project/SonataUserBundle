<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\UserBundle\DependencyInjection\SonataUserExtension;

class SonataUserExtensionTest extends AbstractExtensionTestCase
{
    public function testLoadDefault()
    {
        $this->setParameter('kernel.bundles', array());
        $this->setParameter('twig.form.resources', array());
        $this->load();

        $this->assertContainerBuilderHasAlias(
            'sonata.user.user_manager',
            'sonata.user.orm.user_manager'
        );

        $this->assertContainerBuilderHasAlias(
            'sonata.user.group_manager',
            'sonata.user.orm.group_manager'
        );
    }

    protected function getContainerExtensions()
    {
        return array(
            new SonataUserExtension(),
        );
    }
}
