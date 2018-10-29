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

namespace Sonata\UserBundle\Tests\Action;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Action\CheckLoginAction;

class CheckLoginActionTest extends TestCase
{
    public function testAction(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');

        $action = new CheckLoginAction();
        $action();
    }
}
