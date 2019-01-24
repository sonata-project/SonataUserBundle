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
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\UserBundle\Action\RequestAction;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RequestActionTest extends TestCase
{
    /**
     * @var EngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templating;

    /**
     * @var UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlGenerator;

    /**
     * @var AuthorizationCheckerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorizationChecker;

    /**
     * @var Pool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pool;

    /**
     * @var TemplateRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateRegistry;

    public function setUp(): void
    {
        $this->templating = $this->createMock(EngineInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->pool = $this->createMock(Pool::class);
        $this->templateRegistry = $this->createMock(TemplateRegistryInterface::class);
    }

    public function testAuthenticated(): void
    {
        $request = new Request();

        $this->authorizationChecker->expects($this->once())
            ->method('isGranted')
            ->willReturn(true);

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('sonata_admin_dashboard')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/foo', $result->getTargetUrl());
    }

    public function testUnauthenticated(): void
    {
        $request = new Request();
        $response = $this->createMock(Response::class);

        $parameters = [
            'base_template' => 'base.html.twig',
            'admin_pool' => $this->pool,
        ];

        $this->authorizationChecker->expects($this->once())
            ->method('isGranted')
            ->willReturn(false);

        $this->templating->expects($this->once())
            ->method('renderResponse')
            ->with('@SonataUser/Admin/Security/Resetting/request.html.twig', $parameters)
            ->willReturn($response);

        $this->templateRegistry->expects($this->any())
            ->method('getTemplate')
            ->with('layout')
            ->willReturn('base.html.twig');

        $action = $this->getAction();
        $result = $action($request);

        $this->assertSame($response, $result);
    }

    private function getAction(): RequestAction
    {
        return new RequestAction(
            $this->templating,
            $this->urlGenerator,
            $this->authorizationChecker,
            $this->pool,
            $this->templateRegistry
        );
    }
}
