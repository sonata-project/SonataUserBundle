<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Tests\Controller;

use Sonata\UserBundle\Controller\RegistrationFOSUser1Controller;
use Sonata\UserBundle\Tests\Helpers\PHPUnit_Framework_TestCase;

class RegistrationFOSUser1ControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    protected function setUp()
    {
        $this->controller = new RegistrationFOSUser1Controller();
    }

    public function testItIsInstantiable()
    {
        $this->assertNotNull($this->controller);
    }
}
