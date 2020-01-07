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
use Sonata\UserBundle\Action\ProfileDashboardAction;
use Sonata\UserBundle\DependencyInjection\Configuration;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Twig\Environment;

class ProfileDashboardActionTest extends TestCase
{
    public function setUp(): void
    {
        $this->templating = $this->createMock(Environment::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->profileConfiguration = $this->getMinimalProfileConfiguration();
    }

    public function testAuthenticated(): void
    {
        $userMock = $this->createMock(UserInterface::class);
        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock
            ->method('getUser')
            ->willReturn($userMock);

        $this->tokenStorage
            ->method('getToken')
            ->willReturn($tokenMock);

        $action = $this->getAction();
        $result = $action();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    public function testUnauthenticated(): void
    {
        $this->tokenStorage
            ->method('getToken')
            ->willReturn(null);

        $action = $this->getAction();
        $result = $action();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertNotSame(500, $result->getStatusCode());
    }

    protected function getMinimalProfileConfiguration(): array
    {
        $userBundleConfiguration = (new Processor())->process((new Configuration())->getConfigTreeBuilder()->buildTree(), []);

        return $userBundleConfiguration['profile'];
    }

    private function getAction(): ProfileDashboardAction
    {
        return new ProfileDashboardAction(
            $this->templating,
            $this->tokenStorage,
            $this->profileConfiguration['blocks']
        );
    }
}
