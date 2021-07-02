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

namespace Sonata\UserBundle\Tests\Util;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Tests\Entity\User;
use Sonata\UserBundle\Util\CanonicalFieldsUpdater;

class CanonicalFieldsUpdaterTest extends TestCase
{
    /**
     * @var CanonicalFieldsUpdater
     */
    private $updater;
    private $usernameCanonicalizer;
    private $emailCanonicalizer;

    protected function setUp(): void
    {
        $this->usernameCanonicalizer = $this->getMockCanonicalizer();
        $this->emailCanonicalizer = $this->getMockCanonicalizer();

        $this->updater = new CanonicalFieldsUpdater($this->usernameCanonicalizer, $this->emailCanonicalizer);
    }

    public function testUpdateCanonicalFields()
    {
        $user = new User();
        $user->setUsername('Username');
        $user->setEmail('User@Example.com');

        $this->usernameCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('Username')
            ->willReturnCallback('strtolower');

        $this->emailCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('User@Example.com')
            ->willReturnCallback('strtolower');

        $this->updater->updateCanonicalFields($user);
        $this->assertSame('username', $user->getUsernameCanonical());
        $this->assertSame('user@example.com', $user->getEmailCanonical());
    }

    private function getMockCanonicalizer()
    {
        return $this->getMockBuilder('Sonata\UserBundle\Util\CanonicalizerInterface')->getMock();
    }
}
